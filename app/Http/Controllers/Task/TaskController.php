<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskDeleteRequest;
use App\Http\Requests\Task\TaskIndexRequest;
use App\Http\Requests\Task\TaskShowRequest;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Models\Task;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(TaskIndexRequest $request): JsonResponse
    {
        $perPage = min($request->input('per_page', config('pagination.default_per_page')),
            config('pagination.max_per_page'));
        $tasksDTO = $this->taskService->getAllTasksWithPipeline($request->all(), $perPage);

        return response()->json($tasksDTO);
    }

    public function store(TaskStoreRequest $request): JsonResponse
    {
        $taskDTO = $this->taskService->createTask($request->validated());

        return response()->json([
            'data' => $taskDTO,
            'message' => 'Task created successfully',
        ], 201);
    }

    public function show(TaskShowRequest $request, Task $task): JsonResponse
    {
        $taskDTO = $this->taskService->getTask($task->id, $request->validated());

        return response()->json($taskDTO);
    }

    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        $taskDTO = $this->taskService->updateTask($task->id, $request->validated());

        return response()->json([
            'data' => $taskDTO,
            'message' => 'Task updated successfully',
        ], 200);
    }

    public function destroy(TaskDeleteRequest $request, Task $task): JsonResponse
    {
        $this->taskService->deleteTask($task->id, $request->validated());

        return response()->json(null, 204);
    }
}
