<?php

namespace Tests\Feature;

use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_processes_donation_and_updates_campaign_total(): void
    {
        $response = $this
            ->withHeaders(['X-Idempotency-Key' => 'don-001'])
            ->postJson('/api/donations', [
                'campaign_id' => 100,
                'amount' => 250000,
                'donor_name' => 'Fajar',
                'is_anonymous' => false,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.campaign_id', 100)
            ->assertJsonPath('data.amount', 250000);

        $this->getJson('/api/campaigns/100/donations/total')
            ->assertOk()
            ->assertJsonPath('total_donations', 250000);
    }

    public function test_supports_anonymous_donation(): void
    {
        $response = $this->postJson('/api/donations', [
            'campaign_id' => 42,
            'amount' => 50000,
            'is_anonymous' => true,
            'donor_name' => 'Should Be Hidden',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.is_anonymous', true)
            ->assertJsonPath('data.donor_name', 'Anonymous');
    }

    public function test_idempotency_key_prevents_double_donation_processing(): void
    {
        $payload = [
            'campaign_id' => 11,
            'amount' => 100000,
            'is_anonymous' => false,
            'donor_name' => 'Duplicated Request',
        ];

        $firstResponse = $this
            ->withHeaders(['X-Idempotency-Key' => 'repeat-123'])
            ->postJson('/api/donations', $payload);

        $secondResponse = $this
            ->withHeaders(['X-Idempotency-Key' => 'repeat-123'])
            ->postJson('/api/donations', $payload);

        $firstResponse->assertStatus(201);
        $secondResponse->assertStatus(201);

        $this->assertSame(
            $firstResponse->json('data.id'),
            $secondResponse->json('data.id')
        );

        $this->assertDatabaseCount('donations', 1);

        $donation = Donation::query()->firstOrFail();
        $this->assertSame(100000, $donation->amount);

        $this->getJson('/api/campaigns/11/donations/total')
            ->assertOk()
            ->assertJsonPath('total_donations', 100000);
    }
}
