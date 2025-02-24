<?php

namespace App\Services\Task;

use App\DTO\TaskDTO;
use App\Mappers\TaskMapper;
use App\Pipelines\Filters\Task\DateRangeFilter;
use App\Pipelines\Filters\Task\SortFilter;
use App\Pipelines\Filters\Task\StatusFilter;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class TaskService
{
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getAllTasksWithPipeline(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->taskRepository->query();
        $payload = [$query, $filters];
        $result = app(Pipeline::class)
            ->send($payload)
            ->through([
                DateRangeFilter::class,
                StatusFilter::class,
                SortFilter::class,
            ])
            ->thenReturn();
        [$queryAfterFilters] = $result;

        return $queryAfterFilters->paginate($perPage)
            ->through(fn ($task) => TaskMapper::toDTO($task->toArray()));
    }

    public function createTask(array $data): TaskDTO
    {
        $task = $this->taskRepository->create($data);

        return TaskMapper::toDTO($task->toArray());
    }

    public function getTask(int $id): TaskDTO
    {
        $task = $this->taskRepository->findById($id);

        return TaskMapper::toDTO($task->toArray());
    }

    public function updateTask(int $id, array $data): TaskDTO
    {
        $task = $this->taskRepository->update($id, $data);

        return TaskMapper::toDTO($task->toArray());
    }

    public function deleteTask(int $id): bool
    {
        $task = $this->taskRepository->findById($id);

        return $this->taskRepository->delete($id);
    }
}
