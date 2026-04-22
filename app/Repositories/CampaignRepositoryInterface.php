<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Collection;

interface CampaignRepositoryInterface
{
    public function getAll(): Collection;

    public function create(array $data): Campaign;

    public function update(Campaign $campaign, array $data): bool;

    public function delete(Campaign $campaign): bool;

    public function updateStatus(Campaign $campaign, string $status): bool;

    public function getByStatus(string $status): Collection;
}
