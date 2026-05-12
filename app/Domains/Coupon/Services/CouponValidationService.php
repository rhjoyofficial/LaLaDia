<?php

namespace App\Domains\Coupon\Services;

use App\Domains\Coupon\Models\Coupon;
use App\Domains\Coupon\Models\CouponUsage;
use App\Models\User;
use Exception;

class CouponValidationService
{
    public function validate(
        string $code,
        float $orderAmount,
        ?User $user = null
    ): array {
        // lockForUpdate requires the caller to be inside a DB transaction (OrderService provides this).
        // The lock serialises concurrent coupon applications so both the global
        // usage_limit check AND the per-user limit_per_user check are race-safe.
        $coupon = Coupon::where('code', $code)->lockForUpdate()->first();

        if (! $coupon) {
            throw new Exception('Invalid coupon code');
        }

        // Basic validity (active, date range, global usage limit)
        if (! $coupon->isValid()) {
            throw new Exception('Coupon expired, inactive or usage limit reached');
        }

        // Per-user limit — checked under the same row-level lock so two
        // concurrent requests from the same account can't both pass.
        if ($user && $coupon->limit_per_user) {
            $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $user->id)
                ->count();

            if ($userUsageCount >= $coupon->limit_per_user) {
                throw new Exception('You have already used this coupon the maximum number of times');
            }
        }

        if ($coupon->min_purchase && $orderAmount < $coupon->min_purchase) {
            throw new Exception('Minimum purchase not met');
        }

        return [
            'coupon'   => $coupon,
            'discount' => $this->calculateDiscount($coupon, $orderAmount),
        ];
    }

    private function calculateDiscount(Coupon $coupon, float $amount): float
    {
        if ($coupon->type === 'percentage') {
            return ($amount * $coupon->value) / 100;
        }

        // Cap fixed discount at order amount to prevent discount_total exceeding subtotal
        return min($coupon->value, $amount);
    }
}

