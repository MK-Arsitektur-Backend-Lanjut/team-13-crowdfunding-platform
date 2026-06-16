<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;

class CampaignRepository implements CampaignRepositoryInterface
{
    private const CACHE_TTL = 300; // 5 minutes

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $cacheKey = "campaigns:all:v2:per_page:{$perPage}:page:{$page}";

        $callback = function () use ($perPage): LengthAwarePaginator {
            return Campaign::query()
                ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
                ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
                ->latest('campaigns.created_at')
                ->paginate($perPage);
        };

        try {
            return Cache::tags(['campaigns'])->remember($cacheKey, self::CACHE_TTL, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($cacheKey, self::CACHE_TTL, $callback);
        }
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
        $page = Paginator::resolveCurrentPage();
        $cacheKey = "campaigns:status:{$status}:v2:per_page:{$perPage}:page:{$page}";

        $callback = function () use ($status, $perPage): LengthAwarePaginator {
            return Campaign::query()
                ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
                ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
                ->where('campaigns.status', $status)
                ->latest('campaigns.created_at')
                ->paginate($perPage);
        };

        try {
            return Cache::tags(['campaigns'])->remember($cacheKey, self::CACHE_TTL, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($cacheKey, self::CACHE_TTL, $callback);
        }
    }

    private function forgetCache(): void
    {
        try {
            Cache::tags(['campaigns'])->flush();
        } catch (\BadMethodCallException $e) {
            // Fallback clear for drivers that don't support tags
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget("campaigns:all:v2:per_page:15:page:{$page}");
                Cache::forget("campaigns:status:aktif:v2:per_page:15:page:{$page}");
                Cache::forget("campaigns:status:selesai:v2:per_page:15:page:{$page}");
            }
        }
    }
}