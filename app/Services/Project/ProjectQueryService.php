<?php

namespace App\Services\Project;

use App\DTO\ProjectDTO;
use App\Mappers\ProjectMapper;
use App\Repositories\Interfaces\ProjectRepositoryInterface;

class ProjectQueryService
{
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getProjectByTaskId(int $taskId): ProjectDTO
    {
        $project = $this->projectRepository->findProjectByTaskId($taskId);

        return ProjectMapper::toDTO($project->toArray());
    }
}
