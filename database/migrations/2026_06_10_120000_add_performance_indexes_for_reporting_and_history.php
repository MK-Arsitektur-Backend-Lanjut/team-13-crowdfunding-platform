<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'donations_user_status_idx');
            $table->index(['user_id', 'created_at'], 'donations_user_created_at_idx');
            $table->index(['user_id', 'campaign_id', 'created_at'], 'donations_user_campaign_created_at_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'is_verified'], 'users_role_is_verified_idx');
            $table->index(['role', 'is_verified', 'email'], 'users_role_is_verified_email_idx');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'campaigns_status_created_at_idx');
            $table->index(['created_at'], 'campaigns_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex('campaigns_created_at_idx');
            $table->dropIndex('campaigns_status_created_at_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_is_verified_email_idx');
            $table->dropIndex('users_role_is_verified_idx');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex('donations_user_campaign_created_at_idx');
            $table->dropIndex('donations_user_created_at_idx');
            $table->dropIndex('donations_user_status_idx');
        });
    }
};
