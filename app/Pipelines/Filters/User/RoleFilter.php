<?php

namespace App\Pipelines\Filters\User;

use App\Enums\UserRole;
use Closure;

class RoleFilter
{
    public function handle($payload, Closure $next)
    {
        [$query, $filters] = $payload;
        $role = $filters['role'] ?? null;

        if ($role && in_array($role, UserRole::values())) {
            $query->where('status', $role);
        }

        return $next([$query, $filters]);
    }
}
