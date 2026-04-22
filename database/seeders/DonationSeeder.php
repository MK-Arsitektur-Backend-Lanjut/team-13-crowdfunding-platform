<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('donations')->delete();
        DB::table('donation_totals')->delete();

        $users = User::factory(2000)->create(['email_verified_at' => now()]);

        $userIds = $users->pluck('id')->all();
        $totalRows = 20000;
        $campaignRange = 250;
        $batchSize = 1000;

        $batch = [];

        for ($i = 0; $i < $totalRows; $i++) {
            $isAnonymous = random_int(1, 100) <= 35;
            $amount = random_int(10000, 3000000);

            $batch[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'campaign_id' => random_int(1, $campaignRange),
                'amount' => $amount,
                'status' => 'success',
                'note' => fake()->optional(0.4)->sentence(),
                'is_anonymous' => $isAnonymous,
                'donor_name' => $isAnonymous ? 'Anonymous' : fake()->name(),
                'idempotency_key' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('donations')->insert($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            DB::table('donations')->insert($batch);
        }

        $totals = DB::table('donations')
            ->selectRaw('campaign_id, SUM(amount) as total_amount')
            ->where('status', 'success')
            ->groupBy('campaign_id')
            ->get();

        $totalRowsInsert = $totals
            ->map(fn ($row): array => [
                'campaign_id' => (int) $row->campaign_id,
                'total_amount' => (int) $row->total_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        if ($totalRowsInsert !== []) {
            DB::table('donation_totals')->insert($totalRowsInsert);
        }
    }
}
