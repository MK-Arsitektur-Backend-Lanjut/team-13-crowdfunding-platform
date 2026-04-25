<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('donations')->delete();
        DB::table('donation_totals')->delete();

        $campaignIds = DB::table('campaigns')->pluck('id')->all();

        if ($campaignIds === []) {
            $campaignBatch = [];

            for ($i = 1; $i <= 50; $i++) {
                $campaignBatch[] = [
                    'title' => 'Campaign Seed ' . $i,
                    'description' => 'Campaign otomatis untuk kebutuhan seeding donasi.',
                    'target_amount' => random_int(50000000, 500000000),
                    'status' => random_int(1, 100) <= 75 ? 'aktif' : 'selesai',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('campaigns')->insert($campaignBatch);
            $campaignIds = DB::table('campaigns')->pluck('id')->all();
        }

        DB::table('users')->where('email', 'like', 'donor%@seed.local')->delete();

        $activeDonorCount = 20000;
        $batchSize = 1000;
        $defaultPassword = Hash::make('SeedDonor123!');
        $userInsertBatch = [];

        for ($i = 1; $i <= $activeDonorCount; $i++) {
            $email = 'donor' . str_pad((string) $i, 5, '0', STR_PAD_LEFT) . '@seed.local';

            $userInsertBatch[] = [
                'name' => 'Donor Seed ' . $i,
                'email' => $email,
                'password' => $defaultPassword,
                'role' => 'donor',
                'is_verified' => true,
                'email_verified_at' => $now,
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($userInsertBatch) >= $batchSize) {
                DB::table('users')->insert($userInsertBatch);
                $userInsertBatch = [];
            }
        }

        if ($userInsertBatch !== []) {
            DB::table('users')->insert($userInsertBatch);
        }

        $donors = DB::table('users')
            ->select(['id', 'name'])
            ->where('email', 'like', 'donor%@seed.local')
            ->where('role', 'donor')
            ->where('is_verified', true)
            ->orderBy('id')
            ->get();

        $extraDonationRows = 40000;
        $totalRows = $donors->count() + $extraDonationRows;
        $donationBatch = [];

        for ($i = 0; $i < $totalRows; $i++) {
            $isGuaranteedActiveDonorRow = $i < $donors->count();
            $donor = $isGuaranteedActiveDonorRow
                ? $donors[$i]
                : $donors[random_int(0, $donors->count() - 1)];
            $isAnonymous = random_int(1, 100) <= 35;
            $amount = random_int(10000, 3000000);

            $donationBatch[] = [
                'user_id' => (int) $donor->id,
                'campaign_id' => $campaignIds[random_int(0, count($campaignIds) - 1)],
                'amount' => $amount,
                'status' => 'success',
                'note' => fake()->optional(0.4)->sentence(),
                'is_anonymous' => $isAnonymous,
                'donor_name' => $isAnonymous ? 'Anonymous' : (string) $donor->name,
                'idempotency_key' => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($donationBatch) >= $batchSize) {
                DB::table('donations')->insert($donationBatch);
                $donationBatch = [];
            }
        }

        if ($donationBatch !== []) {
            DB::table('donations')->insert($donationBatch);
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
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($totalRowsInsert !== []) {
            DB::table('donation_totals')->insert($totalRowsInsert);
        }
    }
}
