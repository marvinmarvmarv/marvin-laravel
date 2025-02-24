<?php

namespace App\Pipelines\Filters\Task;

use Closure;

class SortFilter
{
    public function handle($payload, Closure $next)
    {
        [$query, $filters] = $payload;

        if (isset($filters['sort_by'])) {
            $direction = $filters['sort_direction'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $direction);
        }

        return $next([$query, $filters]);
    }
}
