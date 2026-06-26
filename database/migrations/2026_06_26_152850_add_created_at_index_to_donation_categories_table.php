<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_categories', function (Blueprint $table) {
            $table->index('created_at', 'donation_categories_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('donation_categories', function (Blueprint $table) {
            $table->dropIndex('donation_categories_created_at_idx');
        });
    }
};