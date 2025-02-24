<?php

namespace App\Console\Commands;

namespace App\Console\Commands;

use App\Events\TaskExpired;
use App\Models\Task;
use Illuminate\Console\Command;

class TriggerTaskExpiredEvent extends Command
{
    protected $signature = 'event:task-expired';

    protected $description = 'Trigger TaskExpired Event';

    public function handle()
    {
        $task = Task::where('deadline', '<', now())->where('status', '!=', 'done')->first();

        if (!$task) {
            $this->info('no tasks found.');

            return;
        }

        event(new TaskExpired($task));
    }
}
