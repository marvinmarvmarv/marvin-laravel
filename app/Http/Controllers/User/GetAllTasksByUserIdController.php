<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\GetAllTasksByUserIdRequest;
use App\Models\User;
use App\Services\User\UserQueryService;
use Illuminate\Http\JsonResponse;

class GetAllTasksByUserIdController
{
    public function __construct(private UserQueryService $userQueryService) {}

    public function __invoke(GetAllTasksByUserIdRequest $request, User $user): JsonResponse
    {
        $tasksDTO = $this->userQueryService->findAllTasksByUserId($user->id, $request->validated());

        return response()->json($tasksDTO);
    }
}
