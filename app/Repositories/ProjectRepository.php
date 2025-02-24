<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function all(): Collection
    {
        return Project::all();
    }

    public function findById($id): Project
    {
        return Project::findOrFail($id);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(int $id, array $data): Project
    {
        $project = Project::findOrFail($id);
        $project->update($data);

        return $project->fresh();
    }

    public function delete(int $id): bool
    {
        $project = Project::findOrFail($id);

        return $project->delete();
    }

    public function findProjectByTaskId($taskId): Project
    {
        return Project::whereHas('tasks', function ($query) use ($taskId) {
            $query->where('id', $taskId);
        })->first();
    }

    public function query(): Builder
    {
        return Project::query();
    }
}
