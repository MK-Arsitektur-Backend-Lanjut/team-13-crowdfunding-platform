<?php

namespace App\Repositories;

use App\Contracts\Repositories\DonationRepositoryInterface;
use App\Models\Donation;
use App\Models\DonationTotal;
use Illuminate\Support\Facades\DB;

class EloquentDonationRepository implements DonationRepositoryInterface
{
    public function findByIdempotencyKey(string $idempotencyKey): ?Donation
    {
        return Donation::query()->where('idempotency_key', $idempotencyKey)->first();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Donation
    {
        return Donation::query()->create($data);
    }

    public function incrementCampaignTotal(int $campaignId, int $amount): void
    {
        try {
            // Use atomic UPSERT to avoid pessimistic lock contention.
            // INSERT ... ON DUPLICATE KEY UPDATE is atomic in MySQL/InnoDB.
            DB::affectingStatement(
                'INSERT INTO donation_totals (campaign_id, total_amount, created_at, updated_at) 
                 VALUES (?, ?, NOW(), NOW())
                 ON DUPLICATE KEY UPDATE 
                    total_amount = total_amount + ?,
                    updated_at = NOW()',
                [$campaignId, $amount, $amount]
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to increment campaign total, retrying once', [
                'campaign_id' => $campaignId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            DB::affectingStatement(
                'INSERT INTO donation_totals (campaign_id, total_amount, created_at, updated_at) 
                 VALUES (?, ?, NOW(), NOW())
                 ON DUPLICATE KEY UPDATE 
                    total_amount = total_amount + ?,
                    updated_at = NOW()',
                [$campaignId, $amount, $amount]
            );
        }
    }

    public function getCampaignTotal(int $campaignId): int
    {
        return (int) DonationTotal::query()
            ->where('campaign_id', $campaignId)
            ->value('total_amount');
    }
}