<?php

namespace App\Domains\Coupon\Models;

use App\Domains\Order\Models\Order;
use App\Domains\Product\Models\Combo;
use App\Domains\Product\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'usage_limit',
        'used_count',
        'limit_per_user',
        'start_date',
        'end_date',
        'is_active',
        // Scope
        'applies_to',
        // Benefits
        'is_free_delivery',
        'gift_product_variant_id',
        'gift_quantity',
    ];

    protected $casts = [
        'start_date'       => 'datetime',
        'end_date'         => 'datetime',
        'is_active'        => 'boolean',
        'is_free_delivery' => 'boolean',
        'gift_quantity'    => 'integer',
    ];

    // ── Scope relationships ────────────────────────────────────────────────

    /**
     * Product variants this coupon is restricted to (when applies_to='products').
     */
    public function productVariantScopes(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'coupon_product_variants',
            'coupon_id',
            'product_variant_id',
        );
    }

    /**
     * Combos this coupon is restricted to (when applies_to='combos').
     */
    public function comboScopes(): BelongsToMany
    {
        return $this->belongsToMany(
            Combo::class,
            'coupon_combos',
            'coupon_id',
            'combo_id',
        );
    }

    public function giftVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'gift_product_variant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    // ── Validity helpers ───────────────────────────────────────────────────

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function isValidForUser(?User $user = null): bool
    {
        if (!$this->isValid()) return false;

        if ($user && $this->limit_per_user) {
            if ($this->usages()->where('user_id', $user->id)->count() >= $this->limit_per_user) {
                return false;
            }
        }

        return true;
    }

    // ── Scope helpers ──────────────────────────────────────────────────────

    public function isGlobal(): bool
    {
        return $this->applies_to === 'all';
    }

    public function isScopedToProducts(): bool
    {
        return $this->applies_to === 'products';
    }

    public function isScopedToCombos(): bool
    {
        return $this->applies_to === 'combos';
    }

    // ── Benefit helpers ────────────────────────────────────────────────────

    public function hasGift(): bool
    {
        return !empty($this->gift_product_variant_id);
    }
}
