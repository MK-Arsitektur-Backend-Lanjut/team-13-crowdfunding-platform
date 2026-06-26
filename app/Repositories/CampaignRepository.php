<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CampaignRepository implements CampaignRepositoryInterface
{
    private const CACHE_TTL = 300;

    private const CACHE_VERSION = 'v4';

    private const LOCK_SECONDS = 10;

    private const LOCK_WAIT_SECONDS = 1;

    /** @var list<string> */
    private const LIST_COLUMNS = [
        'campaigns.id',
        'campaigns.title',
        'campaigns.description',
        'campaigns.target_amount',
        'campaigns.status',
        'campaigns.created_at',
        'campaigns.updated_at',
        'donation_totals.total_amount as total_donations',
    ];

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginateCampaigns(null, $perPage);
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginateCampaigns($status, $perPage);
    }

    public function findWithTotalDonations(int $id): array
    {
        $cacheKey = 'campaigns:show:'.self::CACHE_VERSION.":{$id}";

        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        return $this->rememberLocked($cacheKey, function () use ($id) {
            $campaign = $this->listQuery()
                ->where('campaigns.id', $id)
                ->first();

            if ($campaign === null) {
                throw (new ModelNotFoundException())->setModel(Campaign::class, [$id]);
            }

            $payload = $campaign->toArray();
            $payload['total_donations'] = (int) ($payload['total_donations'] ?? 0);

            return $payload;
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

    public function getAllActive()
    {
        return Cache::remember('campaigns.active', 60, function () {
            return Campaign::query()
                ->select([
                    'id',
                    'title',
                    'description',
                    'target_amount',
                    'status',
                    'created_at',
                    'updated_at',
                ])
                ->where('status', 'aktif')
                ->get();
        });
    }

    private function paginateCampaigns(?string $status, int $perPage): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $statusKey = $status ?? 'all';
        $cacheKey = 'campaigns:'.$statusKey.':'.self::CACHE_VERSION.":per_page:{$perPage}:page:{$page}";

        $cached = $this->getFromCache($cacheKey);
        if ($cached !== null) {
            return $this->hydratePaginator($cached, $perPage, $page);
        }

        $cached = $this->rememberLocked($cacheKey, function () use ($status, $perPage, $page) {
            $query = $this->listQuery();

            if ($status !== null) {
                $query->where('campaigns.status', $status);
            }

            $items = (clone $query)
                ->latest('campaigns.created_at')
                ->forPage($page, $perPage)
                ->get();

            $paginator = new ConcretePaginator(
                $items,
                $this->resolveTotalCount($status),
                $perPage,
                $page,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );

            return $this->serializePaginator($paginator);
        });

        return $this->hydratePaginator($cached, $perPage, $page);
    }

    private function listQuery(): Builder
    {
        return Campaign::query()
            ->leftJoin('donation_totals', 'campaigns.id', '=', 'donation_totals.campaign_id')
            ->select(self::LIST_COLUMNS);
    }

    private function resolveTotalCount(?string $status): int
    {
        $cacheKey = $status === null
            ? 'campaigns:count:'.self::CACHE_VERSION.':all'
            : 'campaigns:count:'.self::CACHE_VERSION.":status:{$status}";

        return (int) $this->rememberTagged($cacheKey, function () use ($status) {
            $query = Campaign::query();

            if ($status !== null) {
                $query->where('status', $status);
            }

            return $query->count();
        });
    }

    /**
     * @return array{items: list<array<string, mixed>>, total: int, per_page: int, current_page: int, last_page: int, from: int|null, to: int|null}
     */
    private function serializePaginator(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => collect($paginator->items())
                ->map(fn (Campaign $model) => $model->toArray())
                ->all(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    /**
     * @param array{items: list<array<string, mixed>>, total: int, per_page: int, current_page: int, last_page: int, from: int|null, to: int|null} $data
     */
    private function hydratePaginator(array $data, int $perPage, int $page): LengthAwarePaginator
    {
        return new ConcretePaginator(
            Collection::make($data['items']),
            $data['total'],
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    private function getFromCache(string $key): ?array
    {
        try {
            return Cache::tags(['campaigns'])->get($key);
        } catch (\BadMethodCallException) {
            return Cache::get($key);
        }
    }

    private function putToCache(string $key, array $data): void
    {
        try {
            Cache::tags(['campaigns'])->put($key, $data, self::CACHE_TTL);
        } catch (\BadMethodCallException) {
            Cache::put($key, $data, self::CACHE_TTL);
        }
    }

    /**
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    private function rememberTagged(string $key, callable $callback)
    {
        $cached = $this->getTagged($key);
        if ($cached !== null) {
            return $cached;
        }

        $compute = function () use ($key, $callback) {
            $cached = $this->getTagged($key);
            if ($cached !== null) {
                return $cached;
            }

            $value = $callback();
            $this->putTagged($key, $value);

            return $value;
        };

        try {
            return Cache::lock('lock:'.$key, self::LOCK_SECONDS)->block(
                self::LOCK_WAIT_SECONDS,
                $compute
            );
        } catch (LockTimeoutException) {
            return $compute();
        }
    }

    /**
     * @param  callable(): array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    private function rememberLocked(string $key, callable $callback): array
    {
        $cached = $this->getFromCache($key);
        if ($cached !== null) {
            return $cached;
        }

        $compute = function () use ($key, $callback): array {
            $cached = $this->getFromCache($key);
            if ($cached !== null) {
                return $cached;
            }

            $value = $callback();
            $this->putToCache($key, $value);

            return $value;
        };

        try {
            return Cache::lock('lock:'.$key, self::LOCK_SECONDS)->block(
                self::LOCK_WAIT_SECONDS,
                $compute
            );
        } catch (LockTimeoutException) {
            return $compute();
        }
    }

    private function getTagged(string $key): mixed
    {
        try {
            return Cache::tags(['campaigns'])->get($key);
        } catch (\BadMethodCallException) {
            return Cache::get($key);
        }
    }

    private function putTagged(string $key, mixed $value): void
    {
        try {
            Cache::tags(['campaigns'])->put($key, $value, self::CACHE_TTL);
        } catch (\BadMethodCallException) {
            Cache::put($key, $value, self::CACHE_TTL);
        }
    }

    private function forgetCache(): void
    {
        try {
            Cache::tags(['campaigns'])->flush();
        } catch (\BadMethodCallException) {
            for ($page = 1; $page <= 5; $page++) {
                foreach ([15, 50, 100] as $pp) {
                    Cache::forget('campaigns:all:'.self::CACHE_VERSION.":per_page:{$pp}:page:{$page}");
                    Cache::forget('campaigns:aktif:'.self::CACHE_VERSION.":per_page:{$pp}:page:{$page}");
                    Cache::forget('campaigns:selesai:'.self::CACHE_VERSION.":per_page:{$pp}:page:{$page}");
                }
            }

            Cache::forget('campaigns:count:'.self::CACHE_VERSION.':all');
            Cache::forget('campaigns:count:'.self::CACHE_VERSION.':status:aktif');
            Cache::forget('campaigns:count:'.self::CACHE_VERSION.':status:selesai');
        }
    }
}
