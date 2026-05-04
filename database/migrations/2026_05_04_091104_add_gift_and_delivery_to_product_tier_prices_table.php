<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_tier_prices', function (Blueprint $table) {
            $table->boolean('has_free_delivery')->default(false)->after('discount_value');
            $table->json('free_delivery_zones')->nullable()->after('has_free_delivery');
            
            $table->foreignId('gift_product_variant_id')
                  ->nullable()
                  ->after('free_delivery_zones')
                  ->constrained('product_variants')
                  ->nullOnDelete();
                  
            $table->integer('gift_quantity')->default(1)->after('gift_product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_tier_prices', function (Blueprint $table) {
            $table->dropForeign(['gift_product_variant_id']);
            $table->dropColumn([
                'has_free_delivery',
                'free_delivery_zones',
                'gift_product_variant_id',
                'gift_quantity'
            ]);
        });
    }
};
