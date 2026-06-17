<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_totals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id')->unique();
            $table->bigInteger('total_amount')->default(0);
            $table->timestamps();

            $table->index('total_amount');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_totals');
    }
};
