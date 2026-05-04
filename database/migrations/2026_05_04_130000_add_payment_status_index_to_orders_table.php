<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a standalone index on orders.payment_status.
     *
     * AdminTransactionController runs multiple WHERE payment_status = X queries:
     *   - dashboard revenue stats (CASE WHEN payment_status = ...)
     *   - daily revenue chart (WHERE payment_status = 'paid')
     *   - reconciliation listing (WHERE payment_status IN ...)
     *   - discrepancy export
     *   - summary counts
     *
     * The existing composite index [order_status, customer_phone] does not help
     * these queries. This index makes all payment_status-filtered reads O(log n).
     *
     * Migration is idempotent — safe to re-run.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! $this->hasIndex('orders', 'orders_payment_status_index')) {
                $table->index('payment_status', 'orders_payment_status_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndexIfExists('orders_payment_status_index');
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        ))->isNotEmpty();
    }
};
