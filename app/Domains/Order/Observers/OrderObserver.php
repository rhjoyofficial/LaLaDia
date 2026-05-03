<?php

namespace App\Domains\Order\Observers;

use App\Domains\Order\Models\Order;
use App\Jobs\SendConversionEvents;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if (!$order->wasChanged('order_status')) {
            return;
        }

        if ($order->order_status !== 'confirmed') {
            return;
        }

        // Skip internal/test traffic
        if ($order->test_mode) {
            Log::info("OrderObserver: skipping conversion dispatch for order #{$order->order_number} (test_mode)");
            return;
        }

        if ($this->isExcludedIp($order->ip_address)) {
            Log::info("OrderObserver: skipping conversion dispatch for order #{$order->order_number} (excluded IP)");
            return;
        }

        $order->updateQuietly(['approved_at' => now()]);

        SendConversionEvents::dispatch($order)->delay(now()->addSeconds(5));
    }

    private function isExcludedIp(?string $ip): bool
    {
        if (!$ip) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        return in_array($ip, config('tracking.excluded_ips', []), true);
    }
}
