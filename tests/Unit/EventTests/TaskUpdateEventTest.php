<?php

namespace Tests\Unit\EventTests;

use App\Enums\TaskStatus;
use App\Events\TaskExpired;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskUpdateEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_triggers_task_expired_event_when_task_is_updated()
    {
        Event::spy();

        $tasks = Task::factory()->count(5)->create([
            'deadline' => now()->subDay(),
            'status' => TaskStatus::TODO->value,
        ]);

        foreach ($tasks as $task) {
            $task->update(['status' => TaskStatus::IN_PROGRESS->value]);
        }
        Event::assertDispatched(TaskExpired::class, 5);
        $this->assertTrue(true);
    }
}
