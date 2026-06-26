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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex('campaigns_status_index');
            $table->dropIndex('campaigns_status_created_at_index');
            $table->dropIndex('campaigns_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->index('status', 'campaigns_status_index');
            $table->index(['status', 'created_at'], 'campaigns_status_created_at_index');
            $table->index('created_at', 'campaigns_created_at_index');
        });
    }
};
