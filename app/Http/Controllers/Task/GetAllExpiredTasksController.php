<?php

namespace App\Http\Controllers\Task;

use App\Http\Requests\Task\GetAllExpiredTasksRequest;
use App\Services\Task\TaskQueryService;
use Illuminate\Http\JsonResponse;

class GetAllExpiredTasksController
{
    public function __construct(private TaskQueryService $taskQueryService) {}

    public function __invoke(GetAllExpiredTasksRequest $request): JsonResponse
    {
        $tasksDTO = $this->taskQueryService->findAllExpiredTasks($request->validated());

        return response()->json($tasksDTO);
    }
}
