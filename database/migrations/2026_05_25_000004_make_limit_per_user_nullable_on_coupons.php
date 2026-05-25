<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // limit_per_user was NOT NULL DEFAULT 1, but null is the correct representation
        // of "unlimited per user". Without this, Eloquent create/update with null crashes.
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('limit_per_user')->nullable()->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Restore NULLs to 1 before removing nullable so no data is lost
            \Illuminate\Support\Facades\DB::statement(
                'UPDATE coupons SET limit_per_user = 1 WHERE limit_per_user IS NULL'
            );
            $table->integer('limit_per_user')->nullable(false)->default(1)->change();
        });
    }
};
