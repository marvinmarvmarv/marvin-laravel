<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpiredTaskMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $task = $request->route('task');
        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        if (Carbon::parse($task->deadline)->isFuture()) {
            return response()->json(['message' => 'This task is not expired.'], 403);
        }

        return $next($request);
    }
}
