<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_tier_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('combo_id')->constrained('combos')->cascadeOnDelete();

            $table->integer('min_quantity');
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->boolean('has_free_delivery')->default(false);
            $table->json('free_delivery_zones')->nullable();

            $table->foreignId('gift_product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->integer('gift_quantity')->default(1);

            $table->timestamps();

            $table->unique(['combo_id', 'min_quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_tier_prices');
    }
};
