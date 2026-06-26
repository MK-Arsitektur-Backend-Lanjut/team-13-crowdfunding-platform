<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Cache;

trait CachesPaginatedList
{
    protected function rememberPaginated(string $cacheKey, int $ttl, callable $builder): LengthAwarePaginator
    {
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $this->paginatedListFromArray($cached);
        }

        if ($cached !== null) {
            Cache::forget($cacheKey);
        }

        $paginator = $builder();
        Cache::put($cacheKey, $this->paginatedListToArray($paginator), $ttl);

        return $paginator;
    }

    /**
     * @param class-string<Model> $modelClass
     */
    protected function rememberModel(string $cacheKey, int $ttl, callable $builder, string $modelClass): ?Model
    {
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $this->modelFromCachedArray($cached, $modelClass);
        }

        if ($cached !== null) {
            Cache::forget($cacheKey);
        }

        $model = $builder();

        if ($model === null) {
            return null;
        }

        Cache::put($cacheKey, $model->toArray(), $ttl);

        return $model;
    }
    /**
     * @return array{items: array<int, array<string, mixed>>, total: int, per_page: int, current_page: int, last_page: int}
     */
    protected function paginatedListToArray(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => array_map(
                fn ($item) => $item instanceof Model ? $item->toArray() : (array) $item,
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    /**
     * @param array{items: array<int, array<string, mixed>>, total: int, per_page: int, current_page: int, last_page: int} $payload
     */
    protected function paginatedListFromArray(array $payload): LengthAwarePaginator
    {
        return new Paginator(
            $payload['items'],
            $payload['total'],
            $payload['per_page'],
            $payload['current_page'],
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * @param class-string<Model> $modelClass
     */
    protected function modelFromCachedArray(array $attributes, string $modelClass): Model
    {
        return (new $modelClass)->forceFill($attributes)->syncOriginal();
    }
}
