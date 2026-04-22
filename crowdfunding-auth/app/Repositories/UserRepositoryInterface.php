<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findByEmail($email);

    public function create(array $data);

    public function findById($id);
}