<?php

namespace App\Services;

use App\Contracts\Repositories\DonationRepositoryInterface;
use App\Models\Donation;
use App\Repositories\CampaignRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DonationService
{
    public function __construct(
        private readonly DonationRepositoryInterface $donationRepository,
        private readonly DonationStatsService $donationStatsService,
        private readonly CampaignRepositoryInterface $campaignRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function processDonation(array $payload, ?string $idempotencyKey = null): Donation
    {
        $campaignId = (int) $payload['campaign_id'];
        $amount = (int) $payload['amount'];
        $userId = isset($payload['user_id']) ? (int) $payload['user_id'] : null;

        $donation = DB::transaction(function () use ($payload, $idempotencyKey, $campaignId, $amount, $userId): Donation {
            if ($idempotencyKey !== null && $idempotencyKey !== '') {
                $existing = $this->donationRepository->findByIdempotencyKey($idempotencyKey);

                if ($existing !== null) {
                    return $existing;
                }
            }

            $isAnonymous = (bool) ($payload['is_anonymous'] ?? false);

            $donation = $this->donationRepository->create([
                'user_id' => $userId,
                'campaign_id' => $campaignId,
                'amount' => $amount,
                'status' => 'success',
                'note' => $payload['note'] ?? null,
                'is_anonymous' => $isAnonymous,
                'donor_name' => $isAnonymous ? 'Anonymous' : ($payload['donor_name'] ?? null),
                'idempotency_key' => $idempotencyKey,
            ]);

            $this->donationRepository->incrementCampaignTotal($campaignId, $amount);
            $this->donationStatsService->recordSuccessfulDonation($userId);

            return $donation;
        });

        Cache::forget($this->campaignTotalCacheKey($campaignId));
        $this->campaignRepository->invalidateCache();

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
