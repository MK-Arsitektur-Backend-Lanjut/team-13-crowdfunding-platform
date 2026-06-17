<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);

    public function create(array $data);

    public function findById(int $id);
}
