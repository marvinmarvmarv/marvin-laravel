<?php

namespace App\Services\Project;

use App\DTO\ProjectDTO;
use App\Mappers\ProjectMapper;
use App\Pipelines\Filters\Project\DateRangeFilter;
use App\Pipelines\Filters\Project\SortFilter;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ProjectService
{
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getAllProjectsWithPipeline(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->projectRepository->query();
        $payload = [$query, $filters];
        $result = app(Pipeline::class)
            ->send($payload)
            ->through([
                DateRangeFilter::class,
                SortFilter::class,
            ])
            ->thenReturn();

        [$queryAfterFilters] = $result;

        return $queryAfterFilters->paginate($perPage)
            ->through(fn ($task) => ProjectMapper::toDTO($task->toArray()));
    }

    public function createProject(array $data): ProjectDTO
    {
        $project = $this->projectRepository->create($data);

        return ProjectMapper::toDTO($project->toArray());
    }

    public function getProject(int $id): ProjectDTO
    {
        $project = $this->projectRepository->findById($id);

        return ProjectMapper::toDTO($project->toArray());
    }

    public function updateProject(int $id, array $data): ProjectDTO
    {
        $project = $this->projectRepository->findById($id);
        $this->projectRepository->update($project->id, $data);
        $project->refresh();

        return ProjectMapper::toDTO($project->toArray());
    }

    public function deleteProject(int $id): bool
    {
        $project = $this->projectRepository->findById($id);

        return $this->projectRepository->delete($id);
    }
}
