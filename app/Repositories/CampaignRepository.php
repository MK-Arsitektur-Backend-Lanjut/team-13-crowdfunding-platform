<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Collection;

class CampaignRepository implements CampaignRepositoryInterface
{
    public function getAll(): Collection
    {
        return Campaign::query()->latest()->get();
    }

    public function create(array $data): Campaign
    {
        return Campaign::create($data);
    }

    public function update(Campaign $campaign, array $data): bool
    {
        return $campaign->update($data);
    }

    public function delete(Campaign $campaign): bool
    {
        return $campaign->delete();
    }

    public function updateStatus(Campaign $campaign, string $status): bool
    {
        return $campaign->update(['status' => $status]);
    }

    public function getByStatus(string $status): Collection
    {
        return Campaign::query()
            ->where('status', $status)
            ->latest()
            ->get();
    }
}
