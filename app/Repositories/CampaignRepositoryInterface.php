<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CampaignRepositoryInterface
{
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Campaign;

    public function update(Campaign $campaign, array $data): bool;

    public function delete(Campaign $campaign): bool;

    public function updateStatus(Campaign $campaign, string $status): bool;

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator;
}
