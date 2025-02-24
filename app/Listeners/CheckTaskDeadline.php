<?php

namespace App\Listeners;

use App\Events\TaskExpired;
use App\Notifications\TaskExpiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CheckTaskDeadline implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TaskExpired $event): void
    {
        $task = $event->task;
        $user = $task->user;
        Log::info("TaskExpired Event wurde für Task ID {$event->task->id} ausgelöst.");
        Log::info($task->toArray());
        if ($user) {
            $user->notify(new TaskExpiredNotification($task));
        }
    }
}
