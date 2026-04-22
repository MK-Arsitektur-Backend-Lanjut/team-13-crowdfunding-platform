<?php

namespace App\Repositories;

use App\Models\DonationCategory;
use Illuminate\Database\Eloquent\Collection;

interface DonationCategoryRepositoryInterface
{
    public function getAll(): Collection;

    public function create(array $data): DonationCategory;

    public function update(DonationCategory $category, array $data): bool;

    public function delete(DonationCategory $category): bool;
}
