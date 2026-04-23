<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'role' => 'admin',
                'is_verified' => true,
            ]
        );

        $donor = User::updateOrCreate(
            ['email' => 'donor@example.com'],
            [
                'name' => 'Donor User',
                'password' => 'password',
                'role' => 'donor',
                'is_verified' => true,
            ]
        );

        $organizer = User::updateOrCreate(
            ['email' => 'organizer@example.com'],
            [
                'name' => 'Organizer User',
                'password' => 'password',
                'role' => 'organizer',
                'is_verified' => false,
            ]
        );

        Donation::updateOrCreate([
            'user_id' => $donor->id,
            'campaign_id' => 12,
            'amount' => 20000,
        ]);

        Donation::updateOrCreate([
            'user_id' => $donor->id,
            'campaign_id' => 12,
            'amount' => 50000,
        ]);

        Donation::updateOrCreate([
            'user_id' => $donor->id,
            'campaign_id' => 18,
            'amount' => 75000,
        ]);

        Donation::updateOrCreate([
            'user_id' => $organizer->id,
            'campaign_id' => 30,
            'amount' => 150000,
        ]);
    }
}
