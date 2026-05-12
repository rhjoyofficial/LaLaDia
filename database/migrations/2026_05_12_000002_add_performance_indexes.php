<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── carts: index status for active-cart lookups ──────────────────────
        // Every cart lookup filters by status='active'. Without this, large
        // carts tables are full-scanned on every API request.
        Schema::table('carts', function (Blueprint $table) {
            $table->index(['status', 'user_id'], 'carts_status_user_idx');
            $table->index(['status', 'session_token'], 'carts_status_session_idx');
        });

        // ── orders: composite index for customer order history ───────────────
        // CustomerDashboard::orders() queries by user_id ordered by placed_at.
        // The FK index covers equality but not the sort — this covers both.
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'placed_at'], 'orders_user_placed_idx');
        });

        // ── orders: add missing returned_at timestamp ────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('returned_at')->nullable()->after('cancelled_at');
        });

        // ── activity_log: composite index for causer filtering ───────────────
        // Admin activity log is filtered by causer (admin user). Without an
        // index on (causer_type, causer_id, created_at), every page is a scan.
        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(['causer_type', 'causer_id', 'created_at'], 'al_causer_created_idx');
            $table->index(['subject_type', 'subject_id'], 'al_subject_idx');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_status_user_idx');
            $table->dropIndex('carts_status_session_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_placed_idx');
            $table->dropColumn('returned_at');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('al_causer_created_idx');
            $table->dropIndex('al_subject_idx');
        });
    }
};
