<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;

    public function findById($id): User;

    public function create(array $data): User;

    public function update(int $id, array $data): User;

    public function delete(int $id): bool;

    public function findAllTasksByUserId(int $userId): Collection;

    public function query(): Builder;
}
