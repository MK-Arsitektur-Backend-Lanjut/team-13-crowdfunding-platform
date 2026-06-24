<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;

class CampaignRepository implements CampaignRepositoryInterface
{
    private const CACHE_TTL = 300; // 5 minutes

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $cacheKey = "campaigns:all:v3:per_page:{$perPage}:page:{$page}";

        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $this->hydratePaginator($cached, $perPage, $page);
        }

        $result = Campaign::query()
            ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
            ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
            ->latest('campaigns.created_at')
            ->paginate($perPage);

        $this->putToCache($cacheKey, $this->serializePaginator($result));

        return $result;
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
        $cacheKey = "campaigns:status:{$status}:v3:per_page:{$perPage}:page:{$page}";

        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $this->hydratePaginator($cached, $perPage, $page);
        }

        $result = Campaign::query()
            ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
            ->select('campaigns.*', 'donation_totals.total_amount as total_donations')
            ->where('campaigns.status', $status)
            ->latest('campaigns.created_at')
            ->paginate($perPage);

        $this->putToCache($cacheKey, $this->serializePaginator($result));

        return $result;
    }

    public function getAllActive()
    {
        return Cache::remember('campaigns.active', 60, function () {
            return Campaign::where('status', 'aktif')->get();
        });
    }

    /**
     * Serialize a paginator result into a plain array safe for Redis storage.
     * Avoids storing PHP objects (which cause __PHP_Incomplete_Class on unserialize).
     */
    private function serializePaginator(LengthAwarePaginator $paginator): array
    {
        return [
            'items'        => collect($paginator->items())->map(fn($m) => $m->getAttributes())->all(),
            'total'        => $paginator->total(),
            'per_page'     => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'from'         => $paginator->firstItem(),
            'to'           => $paginator->lastItem(),
        ];
    }

    /**
     * Rebuild a LengthAwarePaginator from a cached plain-array snapshot.
     */
    private function hydratePaginator(array $data, int $perPage, int $page): LengthAwarePaginator
    {
        $items = Collection::make($data['items'])->map(function (array|object $item) {
            if ($item instanceof Campaign) {
                return $item;
            }
            $model = new Campaign();
            $model->setRawAttributes(is_array($item) ? $item : (array) $item, true);
            $model->exists = true;
            return $model;
        });

        $paginator = new ConcretePaginator(
            $items,
            $data['total'],
            $perPage,
            $page,
            [
                'path'     => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        return $paginator;
    }

    /**
     * Read a plain-array snapshot from cache (tagged or fallback).
     */
    private function getFromCache(string $key): ?array
    {
        try {
            return Cache::tags(['campaigns'])->get($key);
        } catch (\BadMethodCallException) {
            return Cache::get($key);
        }
    }

    /**
     * Write a plain-array snapshot to cache (tagged or fallback).
     */
    private function putToCache(string $key, array $data): void
    {
        try {
            Cache::tags(['campaigns'])->put($key, $data, self::CACHE_TTL);
        } catch (\BadMethodCallException) {
            Cache::put($key, $data, self::CACHE_TTL);
        }
    }

    private function forgetCache(): void
    {
        try {
            Cache::tags(['campaigns'])->flush();
        } catch (\BadMethodCallException $e) {
            // Fallback clear for drivers that don't support tags
            for ($page = 1; $page <= 5; $page++) {
                foreach ([15, 50, 100] as $pp) {
                    Cache::forget("campaigns:all:v3:per_page:{$pp}:page:{$page}");
                    Cache::forget("campaigns:status:aktif:v3:per_page:{$pp}:page:{$page}");
                    Cache::forget("campaigns:status:selesai:v3:per_page:{$pp}:page:{$page}");
                }
            }
        }
    }
}