<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserShowRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(UserIndexRequest $request): JsonResponse
    {
        $usersDTO = $this->userService->getAllUsers($request->validated());

        return response()->json($usersDTO);
    }

    public function show(UserShowRequest $request, User $user): JsonResponse
    {
        $userDTO = $this->userService->getUser($user->id, $request->validated());

        return response()->json($userDTO);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $userDTO = $this->userService->createUser($request->validated());

        return response()->json([
            'data' => $userDTO,
            'message' => 'User created successfully',
        ], 201);
    }

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $userDTO = $this->userService->updateUser($user->id, $request->validated());

        return response()->json([
            'data' => $userDTO,
            'message' => 'User updated successfully',
        ], 200);
    }

    public function destroy(UserDeleteRequest $request, User $user): JsonResponse
    {
        $this->userService->deleteUser($user->id, $request->validated());

        return response()->json(null, 204);
    }
}
