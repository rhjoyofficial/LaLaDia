<?php

namespace App\Domains\Coupon\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('coupon.create');
    }

    public function rules(): array
    {
        return [
            // Core
            'code'           => 'required|string|unique:coupons,code|max:50',
            'type'           => 'required|in:fixed,percentage',
            'value'          => 'required|numeric|min:0',
            'min_purchase'   => 'nullable|numeric|min:0',
            'usage_limit'    => 'nullable|integer|min:1',
            'limit_per_user' => 'nullable|integer|min:1',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'is_active'      => 'boolean',

            // Scope
            'applies_to'     => 'nullable|in:all,products,combos',
            'variant_ids'    => 'nullable|array',
            'variant_ids.*'  => 'integer|exists:product_variants,id',
            'combo_ids'      => 'nullable|array',
            'combo_ids.*'    => 'integer|exists:combos,id',

            // Benefits
            'is_free_delivery'         => 'boolean',
            'gift_product_variant_id'  => 'nullable|integer|exists:product_variants,id',
            'gift_quantity'            => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'variant_ids.*.exists' => 'One or more selected product variants do not exist.',
            'combo_ids.*.exists'   => 'One or more selected combos do not exist.',
        ];
    }
}
