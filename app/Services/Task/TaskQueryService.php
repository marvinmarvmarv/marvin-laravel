<?php

namespace App\Services\Task;

use App\Mappers\TaskMapper;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Collection;

class TaskQueryService
{
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function findAllExpiredTasks(): Collection
    {
        return $this->taskRepository->findAllExpiredTasks()->map(function ($tasks) {
            return TaskMapper::toDTO($tasks->toArray());
        });
    }

    public function getAllTasksByProjectId(int $projectId): Collection
    {
        return $this->taskRepository->getAllTasksByProjectId($projectId)->map(function ($tasks) {
            return TaskMapper::toDTO($tasks->toArray());
        });
    }
}
