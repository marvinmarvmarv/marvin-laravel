<?php

namespace Tests\Unit\ListenerTests;

use App\Enums\TaskStatus;
use App\Events\TaskExpired;
use App\Listeners\CheckTaskDeadline;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskExpiredNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckTaskDeadlineTest extends TestCase
{
    public function test_listener_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'deadline' => now()->subDay()->toDateString(),
            'status' => TaskStatus::TODO->value,
        ]);

        $event = new TaskExpired($task);
        $listener = new CheckTaskDeadline;

        $listener->handle($event);

        Notification::assertSentTo($user, TaskExpiredNotification::class);
    }
}
