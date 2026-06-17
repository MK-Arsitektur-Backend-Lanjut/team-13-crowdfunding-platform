<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CampaignRepository implements CampaignRepositoryInterface
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::query()
            ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
            ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
            ->latest('campaigns.created_at')
            ->paginate($perPage);
    }

    public function create(array $data): Campaign
    {
        $campaign = Campaign::create($data);
        $this->forgetCache();

        return $campaign;
    }

    public function update(Campaign $campaign, array $data): bool
    {
        $updated = $campaign->update($data);

        if ($updated) {
            $this->forgetCache();
        }

        return $updated;
    }

    public function delete(Campaign $campaign): bool
    {
        $deleted = $campaign->delete();

        if ($deleted) {
            $this->forgetCache();
        }

        return $deleted;
    }

    public function updateStatus(Campaign $campaign, string $status): bool
    {
        $updated = $campaign->update(['status' => $status]);

        if ($updated) {
            $this->forgetCache();
        }

        return $updated;
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::query()
            ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
            ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
            ->where('campaigns.status', $status)
            ->latest('campaigns.created_at')
            ->paginate($perPage);
    }
}