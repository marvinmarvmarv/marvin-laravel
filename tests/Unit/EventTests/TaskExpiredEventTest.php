<?php

namespace Tests\Unit\EventTests;

use App\Enums\TaskStatus;
use App\Events\TaskExpired;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskExpiredEventTest extends TestCase
{
    public function test_task_expired_event_is_dispatched()
    {
        Event::fake();
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'deadline' => now()->subDay()->toDateString(),
            'status' => TaskStatus::TODO->value,
        ]);

        event(new TaskExpired($task));

        Event::assertDispatched(TaskExpired::class);
    }
}
