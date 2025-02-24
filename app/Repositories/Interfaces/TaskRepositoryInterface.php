<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): Task;

    public function create(array $data): Task;

    public function update(int $id, array $data): Task;

    public function delete(int $id): bool;

    public function findAllExpiredTasks(): Collection;

    public function getAllTasksByProjectId(int $id): Collection;

    public function query(): Builder;
}
