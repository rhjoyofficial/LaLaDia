<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL-specific: multi-table DELETE and IF()-based generated columns are not
        // supported by SQLite. CI environments running SQLite skip this migration.
        if (config('database.default') === 'sqlite') {
            return;
        }

        // Remove duplicate active carts per user before adding the constraint.
        // Keep the most recent one (highest id).
        DB::statement("
            DELETE c1 FROM carts c1
            INNER JOIN carts c2
                ON c1.user_id  = c2.user_id
               AND c1.status   = 'active'
               AND c2.status   = 'active'
               AND c1.id < c2.id
            WHERE c1.user_id IS NOT NULL
        ");

        // MySQL has no partial indexes. A generated column that is non-NULL only when
        // status = 'active' allows a unique index to enforce "one active cart per user"
        // while leaving completed/cancelled carts unconstrained.
        DB::statement("
            ALTER TABLE carts
                ADD COLUMN active_marker TINYINT(1)
                    GENERATED ALWAYS AS (IF(status = 'active' AND user_id IS NOT NULL, 1, NULL)) VIRTUAL
        ");

        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['user_id', 'active_marker'], 'carts_one_active_per_user');
        });
    }

    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            return;
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('carts_one_active_per_user');
            $table->dropColumn('active_marker');
        });
    }
};
