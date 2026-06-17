<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->boolean('is_anonymous')->default(false)->after('note');
            $table->string('donor_name')->nullable()->after('is_anonymous');
            $table->string('idempotency_key')->nullable()->unique()->after('donor_name');

            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'status']);
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn(['is_anonymous', 'donor_name', 'idempotency_key']);
        });
    }
};
