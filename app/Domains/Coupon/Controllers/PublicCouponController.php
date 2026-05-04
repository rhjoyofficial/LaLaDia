<?php

namespace App\Domains\Coupon\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Coupon\Services\CouponValidationService;
use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicCouponController extends Controller
{
    /**
     * Validate a coupon code and return the calculated discount.
     */
    public function validateCoupon(
        Request $request,
        CouponValidationService $service
    ) {
        $request->validate([
            'code'         => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        try {
            // CouponValidationService::validate() uses lockForUpdate() which
            // only works correctly inside a transaction.
            $result = DB::transaction(fn() => $service->validate(
                $request->code,
                (float) $request->order_amount,
            ));

            return ApiResponse::success([
                'valid'     => true,
                'discount'  => $result['discount'],
                'coupon_id' => $result['coupon']->id,
            ], 'Coupon validated successfully');
        } catch (Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                ['valid' => false],
                422
            );
        }
    }
}
