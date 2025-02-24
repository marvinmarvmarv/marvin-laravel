<?php

namespace App\Http\Controllers\Project;

use App\Http\Requests\Project\GetProjectByTaskIdRequest;
use App\Models\Task;
use App\Services\Project\ProjectQueryService;
use Illuminate\Http\JsonResponse;

class GetProjectByTaskIdController
{
    public function __construct(private ProjectQueryService $projectQueryService) {}

    public function __invoke(GetProjectByTaskIdRequest $request, Task $task): JsonResponse
    {
        $projectDTO = $this->projectQueryService->getProjectByTaskId($task->id, $request->validated());

        return response()->json($projectDTO);
    }
}
