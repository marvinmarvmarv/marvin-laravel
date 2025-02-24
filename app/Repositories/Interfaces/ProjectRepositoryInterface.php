<?php

namespace App\Repositories\Interfaces;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface
{
    public function all(): Collection;

    public function findById($id): Project;

    public function create(array $data): Project;

    public function update(int $id, array $data): Project;

    public function delete(int $id): bool;

    public function findProjectByTaskId($taskId): Project;

    public function query(): Builder;
}
