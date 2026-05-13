<?php

namespace App\Domains\Order\Services;

use App\Domains\Coupon\Services\CouponValidationService;
use App\Domains\Order\DTOs\CheckoutPricingResult;
use App\Domains\Product\Models\Combo;
use App\Domains\Product\Models\ProductVariant;
use App\Domains\Product\Services\PricingService;
use App\Domains\Shipping\Models\ShippingZone;
use App\Domains\Shipping\Services\ShippingCalculator;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutPricingService
{
    public function __construct(
        private readonly PricingService $pricingService,
        private readonly CouponValidationService $couponService,
        private readonly ShippingCalculator $shippingCalculator,
    ) {}

    /**
     * Single source of truth for all pricing calculations.
     *
     * Used by:
     *  - POST /checkout         (order creation — inside transaction with locks)
     *  - POST /checkout/preview (display — inside transaction with locks)
     *  - GET  /cart             (cart totals — without coupon/zone)
     *
     * @param  array       $items      [{variant_id, combo_id, quantity}]
     * @param  string|null $couponCode Coupon to apply (null = skip)
     * @param  int|null    $zoneId     Shipping zone (null = skip shipping)
     * @param  User|null   $user       Authenticated user (null = guest)
     * @param  bool        $withLock   Acquire row-level locks on variants
     */
    public function calculate(
        array $items,
        ?string $couponCode = null,
        ?int $zoneId = null,
        ?User $user = null,
        bool $withLock = true,
    ): CheckoutPricingResult {

        // 1. Load all referenced variants (with optional pessimistic lock)
        $variants = $this->loadVariants($items, $withLock);

        // 2. Process each item — build line items, accumulate totals
        $lineItems      = [];
        $subtotal       = 0;
        $tierDiscountTotal = 0;

        $freeShippingOverride = false;
        $autoGifts = [];

        foreach ($items as $item) {
            if (!empty($item['combo_id'])) {
                $result = $this->processComboItem($item, $variants);
            } else {
                $result = $this->processVariantItem($item, $variants);
            }

            $lineItems[]       = $result['line_item'];
            $subtotal         += $result['line_subtotal'];
            $tierDiscountTotal += $result['discount_amount'];

            // Process Free Shipping Override (from either combo or variant)
            if (!empty($result['free_shipping'])) {
                $zones = $result['free_shipping_zones'] ?? [];
                if (empty($zones) || (is_array($zones) && in_array($zoneId, $zones))) {
                    $freeShippingOverride = true;
                }
            }

            // Collect Auto Gifts
            if (!empty($result['gifts'])) {
                foreach ($result['gifts'] as $gift) {
                    $autoGifts[] = $gift;
                }
            }
        }
        
        // Add gift line items; track any that are skipped due to stock exhaustion
        $skippedGifts = [];
        foreach ($autoGifts as $gift) {
            $giftVariant = $variants->get($gift['variant_id']);
            if ($giftVariant && $giftVariant->hasStock($gift['quantity'])) {
                $lineItems[] = [
                    'combo_id'                => null,
                    'variant_id'              => $giftVariant->id,
                    'product_id'              => $giftVariant->product->id,
                    'sku_snapshot'            => $giftVariant->sku,
                    'product_name_snapshot'   => $giftVariant->product->name,
                    'combo_name_snapshot'     => null,
                    'variant_title_snapshot'  => $giftVariant->title,
                    'quantity'                => $gift['quantity'],
                    'original_unit_price'     => 0,
                    'unit_price'              => 0,
                    'total_price'             => 0,
                    'discount_type_snapshot'  => 'Free Gift',
                    'discount_value_snapshot' => 0,
                ];
            } else {
                // Gift stock exhausted — keep the tier discount but note the skip
                $skippedGifts[] = [
                    'variant_id'   => $gift['variant_id'],
                    'quantity'     => $gift['quantity'],
                    'product_name' => $giftVariant?->product?->name ?? 'Gift product',
                ];
            }
        }

        // 3. Coupon (applied to subtotal AFTER tier discounts)
        $couponDiscount = 0;
        $coupon = null;

        $discountedSubtotal = $subtotal - $tierDiscountTotal;

        if ($couponCode) {
            $couponResult   = $this->couponService->validate($couponCode, $discountedSubtotal, $user);
            $coupon         = $couponResult['coupon'];
            $couponDiscount = $couponResult['discount'];
        }

        $shippingCost = 0;
        if ($zoneId) {
            if ($freeShippingOverride) {
                $shippingCost = 0;
            } else {
                $zone = ShippingZone::findOrFail($zoneId);
                $shippingCost = $this->shippingCalculator->calculate($zone, $discountedSubtotal);
            }
        }

        // 5. Grand total
        $grandTotal = max(0, $discountedSubtotal - $couponDiscount) + $shippingCost;

        return new CheckoutPricingResult(
            lineItems: $lineItems,
            subtotal: $subtotal,
            tierDiscountTotal: $tierDiscountTotal,
            couponDiscount: $couponDiscount,
            coupon: $coupon,
            shippingCost: $shippingCost,
            grandTotal: $grandTotal,
            lockedVariants: $variants,
            skippedGifts: $skippedGifts,
        );
    }

    /**
     * Process a combo item: validate stock, apply tier pricing, return line item snapshot.
     */
    private function processComboItem(array $item, Collection $variants): array
    {
        $combo      = Combo::with(['items', 'tierPrices'])->findOrFail($item['combo_id']);
        $basePrice  = $combo->final_price;
        $qty        = $item['quantity'];

        // Validate stock for every component
        foreach ($combo->items as $comboItem) {
            $component = $variants->get($comboItem->product_variant_id);
            if (!$component || !$component->hasStock($comboItem->quantity * $qty)) {
                throw new Exception("Component stock exhausted for bundle: {$combo->title}");
            }
        }

        // Find the best applicable tier (highest min_quantity that is <= $qty)
        $activeTier = $combo->tierPrices
            ->where('min_quantity', '<=', $qty)
            ->sortByDesc('min_quantity')
            ->first();

        $unitPrice      = $basePrice;
        $discountAmount = 0;
        $discountType   = null;
        $discountValue  = null;
        $freeShipping   = $combo->has_free_delivery ?? false;
        $freeShippingZones = $combo->free_delivery_zones ?? [];
        $gifts          = [];

        if ($activeTier) {
            if ($activeTier->discount_type === 'percentage') {
                $unitPrice = round($basePrice - ($basePrice * ($activeTier->discount_value / 100)), 2);
            } else {
                $unitPrice = max(0, $basePrice - $activeTier->discount_value);
            }

            $discountAmount   = ($basePrice - $unitPrice) * $qty;
            $discountType     = $activeTier->discount_type;
            $discountValue    = $activeTier->discount_value;

            if ($activeTier->has_free_delivery) {
                $freeShipping      = true;
                $freeShippingZones = $activeTier->free_delivery_zones ?? [];
            }

            if (!empty($activeTier->gift_product_variant_id)) {
                $gifts[] = [
                    'variant_id' => $activeTier->gift_product_variant_id,
                    'quantity'   => $activeTier->gift_quantity ?? 1,
                ];
            }
        }

        return [
            'line_item' => [
                'combo_id'               => $combo->id,
                'variant_id'             => null,
                'product_id'             => null,
                'sku_snapshot'           => null,
                'product_name_snapshot'  => $combo->title,
                'combo_name_snapshot'    => $combo->title,
                'variant_title_snapshot' => 'Bundle',
                'quantity'               => $qty,
                'original_unit_price'    => $basePrice,
                'unit_price'             => $unitPrice,
                'total_price'            => $unitPrice * $qty,
                'discount_type_snapshot' => $discountType,
                'discount_value_snapshot' => $discountValue,
            ],
            'line_subtotal'       => $basePrice * $qty,
            'discount_amount'     => $discountAmount,
            'free_shipping'       => $freeShipping,
            'free_shipping_zones' => $freeShippingZones,
            'gifts'               => $gifts,
        ];
    }

    /**
     * Process a variant item: validate stock, apply tier pricing, return line item snapshot.
     */
    private function processVariantItem(array $item, Collection $variants): array
    {
        $variant = $variants->get($item['variant_id']);
        $qty     = $item['quantity'];

        if (!$variant) {
            throw new Exception('Invalid product variant selected.');
        }

        if (!$variant->hasStock($qty)) {
            throw new Exception("Insufficient stock for {$variant->title}");
        }

        $pricing = $this->pricingService->calculate($variant, $qty, $variant->tierPrices);

        // FIX: Use original_unit_price from PricingService (which uses variant.final_price)
        // NOT variant.price directly. This ensures sale discounts are included in subtotal.
        $lineSubtotal = $pricing['original_unit_price'] * $qty;

        // Determine if tier grants free shipping or gifts
        $tier = $pricing['tier'] ?? null;
        $freeShipping = false;
        $freeShippingZones = [];
        $gifts = [];

        if ($tier) {
            $freeShipping = $tier->has_free_delivery ?? false;
            $freeShippingZones = $tier->free_delivery_zones ?? [];
            
            if (!empty($tier->gift_product_variant_id)) {
                $gifts[] = [
                    'variant_id' => $tier->gift_product_variant_id,
                    'quantity' => $tier->gift_quantity ?? 1,
                ];
            }
        }

        return [
            'line_item' => [
                'combo_id'               => null,
                'variant_id'             => $variant->id,
                'product_id'             => $variant->product->id,
                'sku_snapshot'           => $variant->sku,
                'product_name_snapshot'  => $variant->product->name,
                'combo_name_snapshot'    => null,
                'variant_title_snapshot' => $variant->title,
                'quantity'               => $qty,
                'original_unit_price'    => $pricing['original_unit_price'],
                'unit_price'             => $pricing['unit_price'],
                'total_price'            => $pricing['total'],
                'discount_type_snapshot' => $pricing['discount_type'],
                'discount_value_snapshot' => $pricing['discount_value'],
            ],
            'line_subtotal'   => $lineSubtotal,
            'discount_amount' => $pricing['discount_amount'],
            'free_shipping'   => $freeShipping,
            'free_shipping_zones' => $freeShippingZones,
            'gifts'           => $gifts,
        ];
    }

    /**
     * Load all variants referenced by items (direct + combo components).
     * Optionally acquires row-level exclusive locks for transactional safety.
     */
    private function loadVariants(array $items, bool $withLock): Collection
    {
        $variantIds = collect($items)->pluck('variant_id')->filter()->unique();

        $comboIds = collect($items)->pluck('combo_id')->filter()->unique();
        if ($comboIds->isNotEmpty()) {
            $comboVariantIds = DB::table('combo_items')
                ->whereIn('combo_id', $comboIds)
                ->pluck('product_variant_id');

            $variantIds = $variantIds->merge($comboVariantIds)->unique();
        }

        // Fetch gift variants linked to the active tiers of the main variants
        if ($variantIds->isNotEmpty()) {
            $giftVariantIds = DB::table('product_tier_prices')
                ->whereIn('variant_id', $variantIds)
                ->whereNotNull('gift_product_variant_id')
                ->pluck('gift_product_variant_id');

            $variantIds = $variantIds->merge($giftVariantIds)->unique();
        }

        // Fetch gift variants linked to the active tiers of any combo items
        if ($comboIds->isNotEmpty()) {
            $comboGiftVariantIds = DB::table('combo_tier_prices')
                ->whereIn('combo_id', $comboIds)
                ->whereNotNull('gift_product_variant_id')
                ->pluck('gift_product_variant_id');

            $variantIds = $variantIds->merge($comboGiftVariantIds)->unique();
        }

        $query = ProductVariant::query()
            ->with(['product', 'tierPrices'])
            ->whereIn('id', $variantIds);

        if ($withLock) {
            $query->lockForUpdate();
        }

        return $query->get()->keyBy('id');
    }
}
