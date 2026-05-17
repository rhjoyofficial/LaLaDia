<?php

namespace App\Domains\Landing\Controllers;

use App\Domains\Landing\Services\LandingCheckoutService;
use App\Domains\Landing\Models\LandingPage;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * LandingCheckoutController — API controller
 *
 * Provides preview (pricing) and checkout (order creation) endpoints
 * for landing page embedded checkout forms.
 */
class LandingCheckoutController extends Controller
{
    public function __construct(
        private readonly LandingCheckoutService $checkoutService,
    ) {}

    /**
     * POST /api/landing/{slug}/preview
     *
     * Returns real-time pricing for the landing page checkout form.
     */
    public function preview(Request $request, string $slug): JsonResponse
    {
        $landing = LandingPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if (! $landing->hasEmbeddedCheckout()) {
            return ApiResponse::error('This page type does not support direct checkout.', null, 422);
        }

        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.variant_id' => ['nullable', 'integer', Rule::exists('product_variants', 'id')->where('is_active', true)],
            'items.*.combo_id'   => ['nullable', 'integer', Rule::exists('combos', 'id')->where('is_active', true)],
            'items.*.quantity'   => 'required|integer|min:1',
            'zone_id'            => 'required|integer|exists:shipping_zones,id',
            'coupon_code'        => 'nullable|string|max:50',
        ]);

        try {
            $user = Auth::guard('web')->user() ?? Auth::guard('sanctum')->user();

            $result = $this->checkoutService->preview(
                items: $validated['items'],
                zoneId: $validated['zone_id'],
                landing: $landing,
                couponCode: $validated['coupon_code'] ?? null,
                user: $user,
            );

            return ApiResponse::success($result, 'Pricing calculated');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), null, 422);
        }
    }

    /**
     * POST /api/landing/{slug}/checkout
     *
     * Creates an order directly from the landing page.
     */
    public function checkout(Request $request, string $slug): JsonResponse
    {
        $landing = LandingPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if (! $landing->hasEmbeddedCheckout()) {
            return ApiResponse::error('This page type does not support direct checkout.', null, 422);
        }

        $validated = $request->validate([
            'customer_name'      => 'required|string|max:100',
            'customer_phone'     => 'required|string|max:20',
            'customer_email'     => 'nullable|email|max:100',
            'address_line'       => 'required|string|max:500',
            'area'               => 'nullable|string|max:100',
            'city'               => 'nullable|string|max:100',
            'zone_id'            => 'required|integer|exists:shipping_zones,id',
            'payment_method'     => 'required|in:cod,sslcommerz',
            'items'              => 'required|array|min:1',
            'items.*.variant_id' => ['nullable', 'integer', Rule::exists('product_variants', 'id')->where('is_active', true)],
            'items.*.combo_id'   => ['nullable', 'integer', Rule::exists('combos', 'id')->where('is_active', true)],
            'items.*.quantity'   => 'required|integer|min:1',
            'coupon_code'        => 'nullable|string|max:50',
        ]);

        try {
            $user = Auth::guard('web')->user() ?? Auth::guard('sanctum')->user();

            $validated = array_merge($validated, [
                'ip_address'       => $request->ip(),
                'fbp'              => $request->cookie('_fbp'),
                'fbc'              => $request->cookie('_fbc'),
                'ga_client_id'     => $request->input('ga_client_id') ?? $request->cookie('_ga'),
                'event_source_url' => $request->header('Referer'),
                'user_agent'       => $request->userAgent(),
                'test_mode'        => !app()->isProduction(),
            ]);

            // Resolve cart to selectively remove purchased items later.
            $cartToken = $request->cookie('bionic_cart_token')
                ?? $request->header('X-Session-Token')
                ?? $request->attributes->get('cart_token');

            /** @var \App\Domains\Cart\Services\CartService $cartService */
            $cartService = app(\App\Domains\Cart\Services\CartService::class);
            
            $cart = null;
            try {
                if ($user) {
                    $cart = $cartService->getCart($user->id, null);
                } elseif ($cartToken) {
                    $cart = $cartService->getCart(null, $cartToken);
                }
            } catch (\Throwable $e) {
                // Ignore missing carts
            }

            $order = $this->checkoutService->checkout($validated, $landing, $user, $cart);

            $order->load(['items.variant.product.category', 'items.combo']);

            $request->session()->put('last_order_id', $order->id);
            $request->session()->put('pending_purchase_event', [
                'event_id'       => 'purchase_' . $order->id,
                'transaction_id' => $order->order_number,
                'value'          => (float) $order->grand_total,
                'currency'       => 'BDT',
                'coupon'         => $validated['coupon_code'] ?? null,
                'items'          => $order->items->map(fn ($item) => [
                    'item_id'       => $item->sku_snapshot
                                        ?? ($item->variant_id ? (string) $item->variant_id : null)
                                        ?? ('combo_' . $item->combo_id),
                    'item_name'     => $item->combo_name_snapshot ?? $item->product_name_snapshot,
                    'item_variant'  => $item->variant_title_snapshot,
                    'item_category' => $item->combo_id
                                        ? 'Combo'
                                        : ($item->variant?->product?->category?->name),
                    'price'         => (float) $item->unit_price,
                    'quantity'      => $item->quantity,
                ])->toArray(),
            ]);

            $redirectUrl = $order->payment_method === 'cod'
                ? route('order.success', ['order' => $order->order_number])
                : route('order.failed') . '?reason=payment_gateway_pending&order=' . $order->order_number;

            return ApiResponse::success([
                'order_number' => $order->order_number,
                'redirect_url' => $redirectUrl,
            ], 'Order placed successfully', 201);
        } catch (Exception $e) {
            Log::error('Landing Checkout Error: ' . $e->getMessage(), [
                'slug'           => $slug,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'zone_id'        => $validated['zone_id'] ?? null,
            ]);

            return ApiResponse::error(
                $e->getMessage() ?: 'Order could not be placed. Please try again.',
                null,
                422,
            );
        }
    }
}
