<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->role === UserRole::ADMIN;
    }

    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->role === UserRole::ADMIN;
    }
}
