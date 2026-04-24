<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthModuleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_creates_user_with_role(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Donor User',
            'email' => 'donor@example.com',
            'password' => 'secret123',
            'role' => 'donor',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'User registered successfully')
            ->assertJsonPath('data.email', 'donor@example.com')
            ->assertJsonPath('data.role', 'donor');

        $this->assertDatabaseHas('users', [
            'email' => 'donor@example.com',
            'role' => 'donor',
            'is_verified' => 0,
        ]);
    }

    public function test_login_returns_jwt_token(): void
    {
        User::factory()->create([
            'name' => 'Login User',
            'email' => 'login@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'donor',
            'is_verified' => false,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'name', 'email', 'role', 'is_verified'],
            ]);
    }

    public function test_verify_requires_authentication(): void
    {
        $organizer = User::factory()->create([
            'role' => 'organizer',
            'is_verified' => false,
        ]);

        $response = $this->postJson('/api/verify/'.$organizer->id);

        $response->assertStatus(401);
    }

    public function test_only_admin_can_verify_organizer(): void
    {
        $donor = User::factory()->create([
            'role' => 'donor',
        ]);

        $organizer = User::factory()->create([
            'role' => 'organizer',
            'is_verified' => false,
        ]);

        $token = JWTAuth::fromUser($donor);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/verify/'.$organizer->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $organizer->id,
            'is_verified' => 0,
        ]);
    }

    public function test_admin_can_verify_organizer(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $organizer = User::factory()->create([
            'role' => 'organizer',
            'is_verified' => false,
        ]);

        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/verify/'.$organizer->id);

        $response->assertOk()
            ->assertJsonPath('message', 'Organizer verified successfully')
            ->assertJsonPath('data.id', $organizer->id)
            ->assertJsonPath('data.is_verified', true);

        $this->assertDatabaseHas('users', [
            'id' => $organizer->id,
            'is_verified' => 1,
        ]);
    }

    public function test_authenticated_user_can_view_own_donation_history_only(): void
    {
        $user = User::factory()->create(['role' => 'donor']);
        $otherUser = User::factory()->create(['role' => 'donor']);

        Donation::create([
            'user_id' => $user->id,
            'campaign_id' => 100,
            'amount' => 50000,
        ]);

        Donation::create([
            'user_id' => $otherUser->id,
            'campaign_id' => 100,
            'amount' => 90000,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/donations/history');

        $response->assertOk()
            ->assertJsonPath('message', 'Donation history fetched successfully')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $user->id);
    }

    public function test_authenticated_user_can_filter_and_paginate_own_donation_history(): void
    {
        $user = User::factory()->create(['role' => 'donor']);

        Donation::create([
            'user_id' => $user->id,
            'campaign_id' => 101,
            'amount' => 10000,
        ]);

        Donation::create([
            'user_id' => $user->id,
            'campaign_id' => 101,
            'amount' => 30000,
        ]);

        Donation::create([
            'user_id' => $user->id,
            'campaign_id' => 202,
            'amount' => 50000,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/donations/history?campaign_id=101&min_amount=20000&per_page=1&sort_by=amount&sort_dir=desc');

        $response->assertOk()
            ->assertJsonPath('message', 'Donation history fetched successfully')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.campaign_id', 101)
            ->assertJsonPath('data.0.amount', '30000.00')
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_authenticated_user_can_view_own_donation_detail(): void
    {
        $user = User::factory()->create(['role' => 'donor']);

        $donation = Donation::create([
            'user_id' => $user->id,
            'campaign_id' => 777,
            'amount' => 25000,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/donations/history/'.$donation->id);

        $response->assertOk()
            ->assertJsonPath('message', 'Donation detail fetched successfully')
            ->assertJsonPath('data.id', $donation->id)
            ->assertJsonPath('data.user_id', $user->id);
    }

    public function test_user_cannot_view_or_delete_other_user_donation_history_item(): void
    {
        $user = User::factory()->create(['role' => 'donor']);
        $otherUser = User::factory()->create(['role' => 'donor']);

        $otherDonation = Donation::create([
            'user_id' => $otherUser->id,
            'campaign_id' => 900,
            'amount' => 120000,
        ]);

        $token = JWTAuth::fromUser($user);

        $showResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/donations/history/'.$otherDonation->id);

        $showResponse->assertStatus(404)
            ->assertJsonPath('message', 'Donation not found');

        $deleteResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson('/api/donations/history/'.$otherDonation->id);

        $deleteResponse->assertStatus(404)
            ->assertJsonPath('message', 'Donation not found');

        $this->assertDatabaseHas('donations', [
            'id' => $otherDonation->id,
        ]);
    }

    public function test_user_can_delete_own_donation_history_item(): void
    {
        $user = User::factory()->create(['role' => 'donor']);

        $donation = Donation::create([
            'user_id' => $user->id,
            'campaign_id' => 321,
            'amount' => 60000,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson('/api/donations/history/'.$donation->id);

        $response->assertOk()
            ->assertJsonPath('message', 'Donation deleted successfully');

        $this->assertDatabaseMissing('donations', [
            'id' => $donation->id,
        ]);
    }

    public function test_user_can_refresh_token(): void
    {
        $user = User::factory()->create(['role' => 'donor']);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/refresh');

        $response->assertOk()
            ->assertJsonPath('message', 'Token refreshed successfully')
            ->assertJsonStructure(['token']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'donor']);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Logout successful');
    }
}
