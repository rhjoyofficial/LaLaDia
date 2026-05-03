<?php

namespace App\Jobs;

use App\Domains\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendConversionEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        $order = $this->order->fresh(['items']);

        if ($this->shouldSkip($order)) {
            return;
        }

        // Guard: only fire once
        if ($order->conversion_fired) {
            return;
        }

        $this->sendToMeta($order);
        $this->sendToGA4($order);

        $order->update(['conversion_fired' => true]);
    }

    private function shouldSkip(Order $order): bool
    {
        if ($order->test_mode) {
            Log::info("SendConversionEvents: skipped order #{$order->order_number} (test_mode)");
            return true;
        }

        if ($this->isExcludedIp($order->ip_address)) {
            Log::info("SendConversionEvents: skipped order #{$order->order_number} (excluded IP: {$order->ip_address})");
            return true;
        }

        return false;
    }

    private function isExcludedIp(?string $ip): bool
    {
        if (!$ip) {
            return false;
        }

        // Always skip private/loopback ranges
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        $excluded = config('tracking.excluded_ips', []);

        return in_array($ip, $excluded, true);
    }

    private function sendToMeta(Order $order): void
    {
        $pixelId     = config('tracking.meta_pixel_id');
        $accessToken = config('tracking.meta_access_token');

        if (!$pixelId || !$accessToken) {
            return;
        }

        $userData = [
            'client_ip_address' => $order->ip_address,
            'client_user_agent' => $order->user_agent,
        ];

        if ($order->customer_phone) {
            $digits = preg_replace('/\D/', '', $order->customer_phone);
            $userData['ph'] = [hash('sha256', $digits)];
        }

        if ($order->customer_email) {
            $userData['em'] = [hash('sha256', strtolower(trim($order->customer_email)))];
        }

        if ($order->fbp) {
            $userData['fbp'] = $order->fbp;
        }

        if ($order->fbc) {
            $userData['fbc'] = $order->fbc;
        }

        $payload = [
            'data' => [[
                'event_name'       => 'Purchase',
                'event_time'       => $order->approved_at?->timestamp ?? now()->timestamp,
                'event_id'         => 'purchase_' . $order->id,
                'event_source_url' => $order->event_source_url,
                'action_source'    => 'website',
                'user_data'        => $userData,
                'custom_data'      => [
                    'currency' => 'BDT',
                    'value'    => (float) $order->grand_total,
                    'order_id' => $order->order_number,
                    'contents' => $order->items->map(fn($item) => [
                        'id'         => (string) ($item->variant_id ?? $item->combo_id),
                        'quantity'   => $item->quantity,
                        'item_price' => (float) $item->unit_price,
                    ])->toArray(),
                    'content_type' => 'product',
                ],
            ]],
        ];

        $testCode = config('tracking.meta_test_event_code');
        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v19.0/{$pixelId}/events", $payload);

            if (!$response->successful()) {
                Log::warning("Meta CAPI failed for order #{$order->order_number}", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Meta CAPI exception for order #{$order->order_number}: " . $e->getMessage());
        }
    }

    private function sendToGA4(Order $order): void
    {
        $measurementId = config('tracking.ga4_measurement_id');
        $apiSecret     = config('tracking.ga4_api_secret');

        if (!$measurementId || !$apiSecret) {
            return;
        }

        $payload = [
            'client_id' => $order->ga_client_id ?? $order->ip_address ?? 'unknown',
            'events'    => [[
                'name'   => 'purchase',
                'params' => [
                    'transaction_id' => $order->order_number,
                    'value'          => (float) $order->grand_total,
                    'currency'       => 'BDT',
                    'items'          => $order->items->map(fn($item) => [
                        'item_id'   => (string) ($item->variant_id ?? $item->combo_id),
                        'item_name' => $item->combo_name_snapshot ?? $item->product_name_snapshot,
                        'price'     => (float) $item->unit_price,
                        'quantity'  => $item->quantity,
                    ])->toArray(),
                ],
            ]],
        ];

        try {
            $response = Http::post(
                "https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}",
                $payload
            );

            if (!$response->successful()) {
                Log::warning("GA4 MP failed for order #{$order->order_number}", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("GA4 MP exception for order #{$order->order_number}: " . $e->getMessage());
        }
    }
}
