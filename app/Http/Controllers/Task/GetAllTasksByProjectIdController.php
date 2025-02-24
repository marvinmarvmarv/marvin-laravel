<?php

namespace App\Http\Controllers\Task;

use App\Http\Requests\Task\GetTasksByProjectIdRequest;
use App\Models\Project;
use App\Services\Task\TaskQueryService;
use Illuminate\Http\JsonResponse;

class GetAllTasksByProjectIdController
{
    public function __construct(private TaskQueryService $taskQueryService) {}

    public function __invoke(GetTasksByProjectIdRequest $request, Project $project): JsonResponse
    {
        $taskDTO = $this->taskQueryService->getAllTasksByProjectId($project->id, $request->validated());

        return response()->json($taskDTO);
    }
}
