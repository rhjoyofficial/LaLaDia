<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original unique(cart_id, variant_id) allows the same combo to be
     * inserted multiple times because MySQL treats NULL != NULL in unique indexes.
     * Replace it with a partial unique enforced at application level and drop
     * the DB-level unique that can't cover both variant and combo items cleanly.
     * The application layer (CartService) already enforces one-row-per-variant
     * and one-row-per-combo; this migration removes the misleading DB constraint.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['cart_id', 'variant_id']);

            // Unique per variant item within a cart (variant_id NOT NULL only).
            // MySQL partial indexes aren't natively supported via Blueprint,
            // so we keep the composite index for query performance only.
            $table->index(['cart_id', 'variant_id'], 'cart_items_cart_variant_idx');
            $table->index(['cart_id', 'combo_id'],   'cart_items_cart_combo_idx');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_variant_idx');
            $table->dropIndex('cart_items_cart_combo_idx');

            $table->unique(['cart_id', 'variant_id']);
        });
    }
};
