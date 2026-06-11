<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('jwt.secret', env('JWT_SECRET', 'testing-jwt-secret-key'));
    }

    public function test_register_creates_user_with_role(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Personal User',
            'email' => 'personal@example.com',
            'password' => 'secret123',
            'role' => 'personal',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'User registered successfully')
            ->assertJsonPath('data.user.email', 'personal@example.com')
            ->assertJsonPath('data.user.role', 'personal');

        $this->assertDatabaseHas('users', [
            'email' => 'personal@example.com',
            'role' => 'personal',
            'is_verified' => 0,
        ]);
    }

    public function test_login_returns_jwt_token(): void
    {
        User::factory()->create([
            'name' => 'Login User',
            'email' => 'login@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'personal',
            'is_verified' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'expires_in_minutes',
                    'user' => ['id', 'name', 'email', 'role', 'is_verified'],
                ],
            ]);
    }

    public function test_only_admin_can_verify_organization(): void
    {
        $personalUser = User::factory()->create([
            'role' => 'personal',
        ]);

        $organization = User::factory()->create([
            'role' => 'organization',
            'is_verified' => false,
        ]);

        $token = JWTAuth::fromUser($personalUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/auth/verify/'.$organization->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $organization->id,
            'is_verified' => 0,
        ]);
    }

    public function test_admin_can_verify_organization(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $organization = User::factory()->create([
            'role' => 'organization',
            'is_verified' => false,
        ]);

        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/auth/verify/'.$organization->id);

        $response->assertOk()
            ->assertJsonPath('message', 'Organization verified successfully')
            ->assertJsonPath('data.user.id', $organization->id)
            ->assertJsonPath('data.user.is_verified', true);

        $this->assertDatabaseHas('users', [
            'id' => $organization->id,
            'is_verified' => 1,
        ]);
    }

    public function test_authenticated_user_can_view_own_donation_history_only(): void
    {
        $user = User::factory()->create(['role' => 'personal']);
        $otherUser = User::factory()->create(['role' => 'personal']);

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
}
