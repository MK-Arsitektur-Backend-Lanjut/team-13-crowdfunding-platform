<?php

namespace App\Services;

use App\Contracts\Repositories\DonationRepositoryInterface;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DonationService
{
    public function __construct(private readonly DonationRepositoryInterface $donationRepository)
    {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function processDonation(array $payload, ?string $idempotencyKey = null): Donation
    {
        $campaignId = (int) $payload['campaign_id'];
        $amount = (int) $payload['amount'];

        // Use a single transaction for both donation creation and total increment
        // to ensure data consistency. The UPSERT is atomic and won't cause long locks.
        $donation = DB::transaction(function () use ($payload, $idempotencyKey, $campaignId, $amount): Donation {
            if ($idempotencyKey !== null && $idempotencyKey !== '') {
                $existing = $this->donationRepository->findByIdempotencyKey($idempotencyKey);

                if ($existing !== null) {
                    return $existing;
                }
            }

            $isAnonymous = (bool) ($payload['is_anonymous'] ?? false);

            $donation = $this->donationRepository->create([
                'user_id' => $payload['user_id'] ?? null,
                'campaign_id' => $campaignId,
                'amount' => $amount,
                'status' => 'success',
                'note' => $payload['note'] ?? null,
                'is_anonymous' => $isAnonymous,
                'donor_name' => $isAnonymous ? 'Anonymous' : ($payload['donor_name'] ?? null),
                'idempotency_key' => $idempotencyKey,
            ]);

            // Increment total inside the same transaction for consistency
            $this->donationRepository->incrementCampaignTotal($campaignId, $amount);

            return $donation;
        });

        // Cache invalidation remains outside transaction
        Cache::forget($this->campaignTotalCacheKey($campaignId));
        Cache::forget('donation:stats:v2');

        return $donation;
    }

    public function getCampaignTotal(int $campaignId): int
    {
        return (int) Cache::remember(
            $this->campaignTotalCacheKey($campaignId),
            now()->addSeconds(10),
            fn (): int => $this->donationRepository->getCampaignTotal($campaignId)
        );
    }

    private function campaignTotalCacheKey(int $campaignId): string
    {
        return "campaign:{$campaignId}:donation_total";
    }
}
