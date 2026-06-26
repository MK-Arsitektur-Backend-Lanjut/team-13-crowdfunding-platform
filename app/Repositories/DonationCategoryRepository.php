<?php

namespace App\Repositories;

use App\Models\DonationCategory;
use App\Support\CachesPaginatedList;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class DonationCategoryRepository implements DonationCategoryRepositoryInterface
{
    use CachesPaginatedList;
    private const CACHE_TTL = 3600;
    private const PER_PAGE = 50;
    private const VERSION_KEY = 'donation_categories:version';

    public function getAll(int $page = 1): LengthAwarePaginator
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        $cacheKey = "donation_categories:v{$version}:page:{$page}";

        return $this->rememberPaginated($cacheKey, self::CACHE_TTL, function () use ($page): LengthAwarePaginator {
            return DonationCategory::query()->latest()->paginate(self::PER_PAGE, ['*'], 'page', $page);
        });
    }

    public function findById(int $id): ?DonationCategory
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        $cacheKey = "donation_categories:single:{$id}:v{$version}";

        return $this->rememberModel($cacheKey, self::CACHE_TTL, function () use ($id): ?DonationCategory {
            return DonationCategory::find($id);
        }, DonationCategory::class);
    }

    public function create(array $data): DonationCategory
    {
        $category = DonationCategory::create($data);
        $this->forgetCache();

        return $category;
    }

    public function update(DonationCategory $category, array $data): bool
    {
        $updated = $category->update($data);

        if ($updated) {
            $this->forgetCache();
        }

        return $updated;
    }

    public function delete(DonationCategory $category): bool
    {
        $deleted = $category->delete();

        if ($deleted) {
            $this->forgetCache();
        }

        return $deleted;
    }

    private function forgetCache(): void
    {
        Cache::increment(self::VERSION_KEY);
    }
}
