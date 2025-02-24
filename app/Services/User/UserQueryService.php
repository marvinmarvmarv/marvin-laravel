<?php

namespace App\Services\User;

use App\Mappers\TaskMapper;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserQueryService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findAllTasksByUserId(int $userId): Collection
    {
        return $this->userRepository->findAllTasksByUserId($userId)->map(function ($tasks) {
            return TaskMapper::toDTO($tasks->toArray());
        });
    }
}
