<?php

namespace App\Services\User;

use App\DTO\UserDTO;
use App\Mappers\UserMapper;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->all()->map(function ($user) {
            return UserMapper::toDTO($user->toArray());
        });
    }

    public function getUser(int $id): UserDTO
    {
        $user = $this->userRepository->findById($id);

        return UserMapper::toDTO($user->toArray());
    }

    public function createUser(array $data): UserDTO
    {
        $user = $this->userRepository->create($data);

        return UserMapper::toDTO($user->toArray());
    }

    public function updateUser(int $id, array $data): UserDTO
    {
        $user = $this->userRepository->update($id, $data);

        return UserMapper::toDTO($user->toArray());
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->findById($id);

        return $this->userRepository->delete($id);
    }
}
