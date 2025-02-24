<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id || $user->role === UserRole::ADMIN;
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id || $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->role === UserRole::ADMIN && $task->isExpired()) {
            return true;
        }
        if ($user->id === $task->user_id && !$task->isExpired()) {
            return true;
        }

        return false;
    }
}
