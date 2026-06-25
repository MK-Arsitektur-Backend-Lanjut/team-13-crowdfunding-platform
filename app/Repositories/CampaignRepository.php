<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CampaignRepository implements CampaignRepositoryInterface
{
    private const CACHE_TTL = 300;
    private const VERSION_KEY = 'campaigns:cache_version';

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        $page = (int) request()->query('page', 1);
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        $cacheKey = "campaigns:all:v{$version}:page:{$page}:per{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($perPage): LengthAwarePaginator {
            return Campaign::query()
                ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
                ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
                ->latest('campaigns.created_at')
                ->paginate($perPage);
        });
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
        $page = (int) request()->query('page', 1);
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        $cacheKey = "campaigns:status:{$status}:v{$version}:page:{$page}:per{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($status, $perPage): LengthAwarePaginator {
            return Campaign::query()
                ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
                ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
                ->where('campaigns.status', $status)
                ->latest('campaigns.created_at')
                ->paginate($perPage);
        });
    }

    private function forgetCache(): void
    {
        Cache::increment(self::VERSION_KEY);
    }
}