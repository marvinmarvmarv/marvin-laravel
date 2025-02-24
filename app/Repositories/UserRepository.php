<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        return User::all();
    }

    public function findById($id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update($data);

        return $user->refresh();
    }

    public function delete(int $id): bool
    {
        $user = User::findOrFail($id);

        return $user->delete();
    }

    public function findAllTasksByUserId(int $userId): Collection
    {
        return Task::where('user_id', $userId)->get();
    }

    public function query(): Builder
    {
        return User::query();
    }
}
