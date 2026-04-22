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
        $response = $this->postJson('/register', [
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

        $response = $this->postJson('/login', [
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

        $response = $this->postJson('/verify/'.$organizer->id);

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
        ])->postJson('/verify/'.$organizer->id);

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
        ])->postJson('/verify/'.$organizer->id);

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
        ])->getJson('/donations/history');

        $response->assertOk()
            ->assertJsonPath('message', 'Donation history fetched successfully')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $user->id);
    }
}
