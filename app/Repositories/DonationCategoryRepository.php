<?php

namespace App\Repositories;

use App\Models\DonationCategory;
use Illuminate\Database\Eloquent\Collection;

class DonationCategoryRepository implements DonationCategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        return DonationCategory::query()->latest()->get();
    }

    public function create(array $data): DonationCategory
    {
        return DonationCategory::create($data);
    }

    public function update(DonationCategory $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(DonationCategory $category): bool
    {
        return $category->delete();
    }
}
