<?php

namespace App\Pipelines\Filters\User;

use Closure;

class DateRangeFilter
{
    public function handle($payload, Closure $next)
    {
        [$query, $filters] = $payload;

        if (isset($filters['from'], $filters['to'])) {
            $query->whereBetween('created_at', [$filters['from'], $filters['to']]);
        }

        return $next([$query, $filters]);
    }
}
