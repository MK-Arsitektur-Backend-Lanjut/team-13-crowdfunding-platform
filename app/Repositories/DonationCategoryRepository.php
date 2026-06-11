<?php

namespace App\Repositories;

use App\Models\DonationCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class DonationCategoryRepository implements DonationCategoryRepositoryInterface
{
    private const CACHE_TTL = 3600; // 1 hour
    private const PER_PAGE = 50;

    public function getAll(int $page = 1): LengthAwarePaginator
    {
        $cacheKey = "donation_categories:all:v1:page:{$page}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($page): LengthAwarePaginator {
            return DonationCategory::query()->latest()->paginate(self::PER_PAGE, ['*'], 'page', $page);
        });
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
        Cache::forget('donation_categories:all:v1:page:1');
    }
}
