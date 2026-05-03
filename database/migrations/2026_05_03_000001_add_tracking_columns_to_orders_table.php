<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('test_mode')->default(false)->after('source');
            $table->string('ip_address')->nullable()->after('test_mode');
            $table->string('fbp')->nullable()->after('ip_address');
            $table->string('fbc')->nullable()->after('fbp');
            $table->string('event_source_url')->nullable()->after('fbc');
            $table->string('user_agent')->nullable()->after('event_source_url');
            $table->string('ga_client_id')->nullable()->after('user_agent');
            $table->boolean('conversion_fired')->default(false)->after('ga_client_id');
            $table->timestamp('approved_at')->nullable()->after('conversion_fired');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'test_mode',
                'ip_address',
                'fbp',
                'fbc',
                'event_source_url',
                'user_agent',
                'ga_client_id',
                'conversion_fired',
                'approved_at',
            ]);
        });
    }
};
