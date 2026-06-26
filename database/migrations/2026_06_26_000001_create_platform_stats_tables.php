<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_stats')) {
            Schema::create('platform_stats', function (Blueprint $table) {
                $table->unsignedTinyInteger('id')->primary();
                $table->unsignedBigInteger('total_success_donations')->default(0);
                $table->unsignedBigInteger('active_donors')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('active_donor_markers')) {
            Schema::create('active_donor_markers', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->primary();
                $table->timestamp('first_donated_at');
            });
        }

        if (DB::table('platform_stats')->where('id', 1)->doesntExist()) {
            $totalSuccessDonations = (int) DB::table('donations')
                ->where('status', 'success')
                ->count();

            $activeDonorUserIds = DB::table('users')
                ->where('role', 'donor')
                ->where('is_verified', true)
                ->whereExists(function ($query): void {
                    $query->select(DB::raw(1))
                        ->from('donations')
                        ->whereColumn('donations.user_id', 'users.id')
                        ->where('donations.status', 'success');
                })
                ->pluck('id');

            $now = now();

            foreach ($activeDonorUserIds as $userId) {
                DB::table('active_donor_markers')->insertOrIgnore([
                    'user_id' => $userId,
                    'first_donated_at' => $now,
                ]);
            }

            DB::table('platform_stats')->insert([
                'id' => 1,
                'total_success_donations' => $totalSuccessDonations,
                'active_donors' => $activeDonorUserIds->count(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('active_donor_markers');
        Schema::dropIfExists('platform_stats');
    }
};
