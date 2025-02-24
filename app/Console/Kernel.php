<?php

namespace App\Console;

use App\Enums\TaskStatus;
use App\Events\TaskExpired;
use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $tasks = Task::where('deadline', '<', today())
                ->where('status', '!=', TaskStatus::DONE->value)
                ->get();

            foreach ($tasks as $task) {
                event(new TaskExpired($task));
            }
        })->dailyAt('00:05');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
