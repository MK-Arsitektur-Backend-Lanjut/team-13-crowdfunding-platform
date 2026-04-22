<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
            }

            if (!Schema::hasColumn('donations', 'campaign_id')) {
                $table->unsignedBigInteger('campaign_id')->default(0)->after('user_id');
            }

            if (!Schema::hasColumn('donations', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0)->after('campaign_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'amount')) {
                $table->dropColumn('amount');
            }

            if (Schema::hasColumn('donations', 'campaign_id')) {
                $table->dropColumn('campaign_id');
            }

            if (Schema::hasColumn('donations', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
