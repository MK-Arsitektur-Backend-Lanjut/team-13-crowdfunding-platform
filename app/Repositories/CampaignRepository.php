<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CampaignRepository implements CampaignRepositoryInterface
{
    private const CACHE_TTL = 300; // 5 minutes
    private const CACHE_STALE_TTL = 60; // 1 minute grace period for stale-while-revalidate
    private const PER_PAGE = 20;

    public function getAll(int $page = 1): LengthAwarePaginator
    {
        $cacheKey = "campaigns:all:v2:page:{$page}";

        return Cache::flexible($cacheKey, [self::CACHE_TTL, self::CACHE_TTL + 60], function () use ($page): LengthAwarePaginator {
            return Campaign::query()->latest()->paginate(self::PER_PAGE, ['*'], 'page', $page);
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

    public function getByStatus(string $status, int $page = 1): LengthAwarePaginator
    {
        $cacheKey = "campaigns:status:{$status}:v2:page:{$page}";

        return Cache::flexible($cacheKey, [self::CACHE_TTL, self::CACHE_TTL + self::CACHE_STALE_TTL], function () use ($status, $page): LengthAwarePaginator {
            return Campaign::query()
                ->where('status', $status)
                ->latest()
                ->paginate(self::PER_PAGE, ['*'], 'page', $page);
        });
    }

    private function forgetCache(): void
    {
        // Flush all campaign list caches - uses pattern-based flush via tags if supported
        // For Redis without tags, clear known keys
        Cache::forget('campaigns:all:v1');
        Cache::forget('campaigns:status:aktif:v1');
        Cache::forget('campaigns:status:selesai:v1');
    }
}
