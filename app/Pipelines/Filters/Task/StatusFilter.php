<?php

namespace App\Pipelines\Filters\Task;

use App\Enums\TaskStatus;
use Closure;

class StatusFilter
{
    public function handle($payload, Closure $next)
    {
        [$query, $filters] = $payload;
        $status = $filters['status'] ?? null;

        if ($status && in_array($status, TaskStatus::values())) {
            $query->where('status', $status);
        }

        return $next([$query, $filters]);
    }
}
