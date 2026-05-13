<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FULLTEXT indexes require MySQL/MariaDB InnoDB — SQLite does not support them.
        // CI environments using SQLite skip this migration gracefully.
        if (config('database.default') === 'sqlite') {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // FULLTEXT index replaces the B-tree index('name') which cannot serve LIKE '%q%' queries.
            // Requires MySQL 5.6+ InnoDB (standard on all modern hosts).
            $table->fullText(['name', 'short_description'], 'products_fulltext_search');

            // Drop the low-value single-column B-tree name index — it was never used for search.
            $table->dropIndex('products_name_index');

            // Drop the low-cardinality boolean-only index — MySQL won't use it.
            $table->dropIndex('products_is_featured_index');
        });
    }

    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText('products_fulltext_search');
            $table->index('name');
            $table->index('is_featured');
        });
    }
};
