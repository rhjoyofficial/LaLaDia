<?php

namespace App\Domains\Coupon\Services;

use App\Domains\Coupon\Models\Coupon;
use App\Domains\Coupon\Models\CouponUsage;
use App\Models\User;
use Exception;

class CouponValidationService
{
    /**
     * Validate a coupon and return the discount + benefit flags.
     *
     * This method MUST be called inside a DB::transaction so that the
     * lockForUpdate() on the coupon row is effective.
     *
     * @param  string     $code         Coupon code to validate.
     * @param  float      $orderAmount  Full discounted subtotal (after tier discounts).
     *                                  Used for min_purchase check and global-scope discount base.
     * @param  User|null  $user         Authenticated user (null = guest, fails per-user check).
     * @param  array      $lineItems    Already-built line items from the pricing engine.
     *                                  Required for scoped coupon (applies_to='products'|'combos')
     *                                  to calculate the applicable discount base and verify the
     *                                  coupon targets at least one item in the cart.
     *                                  Can be empty for backward-compatible callers (global scope only).
     *
     * @return array{
     *   coupon: Coupon,
     *   discount: float,
     *   free_delivery: bool,
     *   gift_variant_id: int|null,
     *   gift_quantity: int,
     * }
     *
     * @throws Exception on any validation failure.
     */
    public function validate(
        string $code,
        float $orderAmount,
        ?User $user = null,
        array $lineItems = [],
    ): array {
        // Row-level lock serialises concurrent coupon applications so both the
        // global usage_limit and the per-user limit_per_user checks are race-safe.
        $coupon = Coupon::where('code', $code)->lockForUpdate()->first();

        if (!$coupon) {
            throw new Exception('Invalid coupon code.');
        }

        // Basic validity: active flag, date range, global usage cap
        if (!$coupon->isValid()) {
            throw new Exception('Coupon is expired, inactive, or has reached its usage limit.');
        }

        // Per-user limit (also under the same row lock)
        if ($user && $coupon->limit_per_user) {
            $used = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $user->id)
                ->count();

            if ($used >= $coupon->limit_per_user) {
                throw new Exception('You have already used this coupon the maximum number of times.');
            }
        }

        // Minimum purchase check — always against the full order amount, not the
        // scoped subset, so customers aren't penalised for adding non-eligible items.
        if ($coupon->min_purchase && $orderAmount < $coupon->min_purchase) {
            throw new Exception(
                'A minimum purchase of ৳' . number_format($coupon->min_purchase, 2) . ' is required for this coupon.'
            );
        }

        // Resolve the amount that the discount will be calculated against.
        // For scoped coupons this is the subtotal of eligible line items only.
        $applicableAmount = $this->resolveApplicableAmount($coupon, $orderAmount, $lineItems);

        // Discount calculation
        $discount = $this->calculateDiscount($coupon, $applicableAmount);

        return [
            'coupon'          => $coupon,
            'discount'        => $discount,
            'free_delivery'   => (bool) $coupon->is_free_delivery,
            'gift_variant_id' => $coupon->gift_product_variant_id,
            'gift_quantity'   => max(1, (int) $coupon->gift_quantity),
        ];
    }

    /**
     * Determine the subtotal the discount applies to.
     *
     * 'all'      → full discounted order subtotal
     * 'products' → sum of total_price for line items whose variant_id is in the coupon's scope
     * 'combos'   → sum of total_price for line items whose combo_id is in the coupon's scope
     *
     * Throws if the coupon is scoped but no eligible line items are in the cart.
     */
    private function resolveApplicableAmount(Coupon $coupon, float $orderAmount, array $lineItems): float
    {
        if ($coupon->isGlobal() || empty($lineItems)) {
            return $orderAmount;
        }

        if ($coupon->isScopedToProducts()) {
            // Eager-loaded by loadCouponScopes() in CheckoutPricingService
            $scopedIds = $coupon->productVariantScopes->pluck('id')->toArray();

            $eligible = array_filter(
                $lineItems,
                fn($l) => !empty($l['variant_id']) && in_array($l['variant_id'], $scopedIds, true)
            );

            if (empty($eligible)) {
                throw new Exception('This coupon is not applicable to the items in your cart.');
            }

            return (float) array_sum(array_column($eligible, 'total_price'));
        }

        if ($coupon->isScopedToCombos()) {
            $scopedIds = $coupon->comboScopes->pluck('id')->toArray();

            $eligible = array_filter(
                $lineItems,
                fn($l) => !empty($l['combo_id']) && in_array($l['combo_id'], $scopedIds, true)
            );

            if (empty($eligible)) {
                throw new Exception('This coupon is not applicable to the items in your cart.');
            }

            return (float) array_sum(array_column($eligible, 'total_price'));
        }

        return $orderAmount;
    }

    private function calculateDiscount(Coupon $coupon, float $amount): float
    {
        if ($amount <= 0) {
            return 0;
        }

        if ($coupon->type === 'percentage') {
            return round(($amount * $coupon->value) / 100, 2);
        }

        // Fixed — cap at the applicable amount so the discount never exceeds it
        return min((float) $coupon->value, $amount);
    }
}
