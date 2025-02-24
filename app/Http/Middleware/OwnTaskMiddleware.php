<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnTaskMiddleware
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
        if ($request->user()->id !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized. This task does not belong to you.'], 403);
        }

        return $next($request);
    }
}
