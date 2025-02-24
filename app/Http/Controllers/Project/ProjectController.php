<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectDeleteRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use App\Http\Requests\Project\ProjectShowRequest;
use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Models\Project;
use App\Services\Project\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(ProjectIndexRequest $request): JsonResponse
    {
        $perPage = min($request->input('per_page', config('pagination.default_per_page')),
            config('pagination.max_per_page'));
        $projectsDTO = $this->projectService->getAllProjectsWithPipeline($request->all(), $perPage);

        return response()->json($projectsDTO);
    }

    public function store(ProjectStoreRequest $request): JsonResponse
    {
        $projectDTO = $this->projectService->createProject($request->validated());

        return response()->json([
            'data' => $projectDTO,
            'message' => 'Project created successfully',
        ], 201);
    }

    public function show(ProjectShowRequest $request, Project $project): JsonResponse
    {
        $projectDTO = $this->projectService->getProject($project->id, $request->validated());

        return response()->json($projectDTO);
    }

    public function update(ProjectUpdateRequest $request, Project $project): JsonResponse
    {
        $projectDTO = $this->projectService->updateProject($project->id, $request->validated());

        return response()->json([
            'data' => $projectDTO,
            'message' => 'Project updated successfully',
        ], 200);
    }

    public function destroy(ProjectDeleteRequest $request, Project $project): JsonResponse
    {
        $this->projectService->deleteProject($project->id, $request->validated());

        return response()->json([
            'message' => 'Project deleted successfully',
        ], 204);
    }
}
