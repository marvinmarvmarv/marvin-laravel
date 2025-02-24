<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }
        if ($project->relationLoaded('tasks')) {
            return $project->tasks->where('user_id', $user->id)->isNotEmpty();
        }

        return $project->tasks()->where('tasks.user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
