<?php

namespace App\Contracts\Repositories;

use App\Models\Donation;

interface DonationRepositoryInterface
{
    public function findByIdempotencyKey(string $idempotencyKey): ?Donation;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Donation;

    public function incrementCampaignTotal(int $campaignId, int $amount): void;

    public function getCampaignTotal(int $campaignId): int;
}
