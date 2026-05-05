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
        Schema::table('combos', function (Blueprint $table) {
            $table->boolean('has_free_delivery')->default(false)->after('discount_value');
            $table->json('free_delivery_zones')->nullable()->after('has_free_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->dropColumn(['has_free_delivery', 'free_delivery_zones']);
        });
    }
};
