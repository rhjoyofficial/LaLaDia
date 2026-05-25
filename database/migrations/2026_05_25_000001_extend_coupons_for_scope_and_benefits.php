<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Scope: which item types this coupon applies to.
            // 'all'      → full discounted subtotal (existing behaviour)
            // 'products' → only product variant line items in coupon_product_variants pivot
            // 'combos'   → only combo line items in coupon_combos pivot
            $table->string('applies_to', 20)->default('all')->after('is_active');

            // Free-delivery benefit — waives shipping independently of discount type.
            // Can be combined with percentage / fixed discounts on the same coupon.
            $table->boolean('is_free_delivery')->default(false)->after('applies_to');

            // Gift benefit — injects a free line item when this coupon is applied.
            $table->foreignId('gift_product_variant_id')
                ->nullable()
                ->after('is_free_delivery')
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->unsignedSmallInteger('gift_quantity')->default(1)->after('gift_product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['gift_product_variant_id']);
            $table->dropColumn(['applies_to', 'is_free_delivery', 'gift_product_variant_id', 'gift_quantity']);
        });
    }
};
