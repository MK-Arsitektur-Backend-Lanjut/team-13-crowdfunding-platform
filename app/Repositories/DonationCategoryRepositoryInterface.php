<?php

namespace App\Repositories;

use App\Models\DonationCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DonationCategoryRepositoryInterface
{
    public function getAll(int $page = 1): LengthAwarePaginator;

    public function create(array $data): DonationCategory;

    public function update(DonationCategory $category, array $data): bool;

    public function delete(DonationCategory $category): bool;
}
