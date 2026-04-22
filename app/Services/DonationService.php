<?php

namespace App\Services;

use App\Contracts\Repositories\DonationRepositoryInterface;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($payload, $idempotencyKey): Donation {
            if ($idempotencyKey !== null && $idempotencyKey !== '') {
                $existing = $this->donationRepository->findByIdempotencyKey($idempotencyKey);

                if ($existing !== null) {
                    return $existing;
                }
            }

            $isAnonymous = (bool) ($payload['is_anonymous'] ?? false);

            $donation = $this->donationRepository->create([
                'user_id' => $payload['user_id'] ?? null,
                'campaign_id' => (int) $payload['campaign_id'],
                'amount' => (int) $payload['amount'],
                'status' => 'success',
                'note' => $payload['note'] ?? null,
                'is_anonymous' => $isAnonymous,
                'donor_name' => $isAnonymous ? 'Anonymous' : ($payload['donor_name'] ?? null),
                'idempotency_key' => $idempotencyKey,
            ]);

            $this->donationRepository->incrementCampaignTotal(
                (int) $payload['campaign_id'],
                (int) $payload['amount']
            );

            return $donation;
        });
    }

    public function getCampaignTotal(int $campaignId): int
    {
        return $this->donationRepository->getCampaignTotal($campaignId);
    }
}
