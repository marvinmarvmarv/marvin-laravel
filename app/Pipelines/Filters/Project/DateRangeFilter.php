<?php

namespace App\Pipelines\Filters\Project;

use Closure;

class DateRangeFilter
{
    public function handle($payload, Closure $next)
    {
        [$query, $filters] = $payload;

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'],
                $filters['end_date'],
            ]);
        }

        return $next([$query, $filters]);
    }
}
