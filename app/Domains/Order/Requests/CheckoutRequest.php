<?php

namespace App\Domains\Order\Requests;

use App\Domains\Cart\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $cartToken = $this->attributes->get('cart_token')
            ?? $this->header('X-Session-Token')
            ?? $this->cookie('bionic_cart_token');

        if (!$this->filled('checkout_token') && !Auth::check()) {
            $this->merge(['checkout_token' => (string) Str::uuid()]);
        }

        if (!$this->has('items') || !is_array($this->input('items')) || count($this->input('items', [])) === 0) {
            /** @var CartService $cartService */
            $cartService = app(CartService::class);
            $cart = $cartService->getCart(Auth::id(), Auth::check() ? null : $cartToken);
            $cart->load('items');

            $items = $cart->items->map(fn($item) => array_filter([
                'variant_id' => $item->variant_id,
                'combo_id'   => $item->combo_id,
                'quantity'   => $item->quantity,
            ], fn($value) => !is_null($value)))->values()->all();

            $this->merge(['items' => $items]);
        }
    }

    public function authorize(): bool
    {
        return true; // guests allowed
    }

    public function rules(): array
    {
        return [
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => ['required', 'string', 'max:20', 'regex:/^(\+88)?01[3-9]\d{8}$/'],
            'customer_email'   => 'nullable|email|max:255',
            'address_line'     => 'required|string|max:500',
            'city'             => 'required|string|max:100',
            'zone_id'          => 'required|exists:shipping_zones,id',
            'payment_method'   => 'required|in:cod',
            // 'payment_method'   => 'required|in:cod,sslcommerz',
            'notes'            => 'nullable|string|max:1000',

            'items'              => 'required|array|min:1',
            'items.*.variant_id' => ['nullable', Rule::exists('product_variants', 'id')->where('is_active', true)],
            'items.*.combo_id'   => ['nullable', Rule::exists('combos', 'id')->where('is_active', true)],
            'items.*.quantity'   => 'required|integer|min:1|max:999',

            'ga_client_id'   => 'nullable|string|max:100',
            'coupon_code'    => 'nullable|string|max:50',
            'checkout_token' => [
                Auth::check() ? 'nullable' : 'required',
                'string',
                'min:32',
            ],
        ];
    }

    /** Ensure every item has at least one of variant_id or combo_id. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            foreach ($this->input('items', []) as $index => $item) {
                if (empty($item['variant_id']) && empty($item['combo_id'])) {
                    $v->errors()->add(
                        "items.{$index}",
                        'Each item must specify either a variant_id or a combo_id.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex'      => 'Phone must be a valid Bangladeshi number (e.g. 01712345678 or +8801712345678).',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min'      => 'Quantity must be at least 1.',
            'items.*.quantity.max'      => 'Quantity may not exceed 999 per item.',
            'zone_id.required'          => 'Please select a delivery zone.',
            'zone_id.exists'            => 'The selected delivery zone is invalid.',
            'payment_method.in'         => 'Invalid payment method selected.',
        ];
    }
}
