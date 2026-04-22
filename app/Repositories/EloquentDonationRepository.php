<?php

namespace App\Repositories;

use App\Contracts\Repositories\DonationRepositoryInterface;
use App\Models\Donation;
use App\Models\DonationTotal;
use Illuminate\Database\QueryException;

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
        $existing = DonationTotal::query()
            ->where('campaign_id', $campaignId)
            ->lockForUpdate()
            ->first();

        if ($existing !== null) {
            $existing->increment('total_amount', $amount);

            return;
        }

        try {
            DonationTotal::query()->create([
                'campaign_id' => $campaignId,
                'total_amount' => $amount,
            ]);
        } catch (QueryException) {
            // Concurrent inserts can hit unique key; fallback to increment existing row.
            DonationTotal::query()
                ->where('campaign_id', $campaignId)
                ->lockForUpdate()
                ->firstOrFail()
                ->increment('total_amount', $amount);
        }
    }

    public function getCampaignTotal(int $campaignId): int
    {
        return (int) DonationTotal::query()
            ->where('campaign_id', $campaignId)
            ->value('total_amount');
    }
}
