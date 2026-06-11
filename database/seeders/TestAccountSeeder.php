<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Test Personal',
                'email' => 'personal@test.local',
                'role' => 'personal',
                'is_verified' => true,
            ],
            [
                'name' => 'Test Organization',
                'email' => 'organization@test.local',
                'role' => 'organization',
                'is_verified' => true,
            ],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('Test12345!'),
                    'role' => $account['role'],
                    'is_verified' => $account['is_verified'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
