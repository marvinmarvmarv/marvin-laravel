<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(): Collection
    {
        return Task::all();
    }

    public function findById(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);
        $task->update($data);

        return $task->fresh();
    }

    public function delete(int $id): bool
    {
        $task = Task::findOrFail($id);

        return $task->delete();
    }

    public function findAllExpiredTasks(): Collection
    {
        return Task::whereNotNull('deadline')
            ->where('deadline', '<', Carbon::today()->toDateString())
            ->get();
    }

    public function getAllTasksByProjectId(int $id): Collection
    {
        return Task::whereNotNull('project_id')
            ->where('project_id', $id)
            ->get();
    }

    public function query(): Builder
    {
        return Task::query();
    }
}
