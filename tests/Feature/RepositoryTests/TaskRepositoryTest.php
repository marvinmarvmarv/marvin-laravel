<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = new TaskRepository(new Task);
    }

    public function test_can_create_task()
    {
        $user = User::factory()->create();
        $taskData = Task::factory()->make([
            'user_id' => $user->id,
        ]);

        $task = $this->taskRepository->create([
            'title' => $taskData->title,
            'description' => $taskData->description,
            'status' => $taskData->status,
            'deadline' => $taskData->deadline,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertDatabaseHas('tasks', ['title' => $taskData->title]);
    }

    public function test_can_get_task()
    {
        $task = Task::factory()->create();

        $foundTask = $this->taskRepository->findById($task->id);
        $this->assertDatabaseHas('tasks', ['id' => $foundTask->id]);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create(['title' => 'Old Title']);

        $updatedTask = $this->taskRepository->update($task->id, ['title' => 'New Title']);

        $this->assertEquals('New Title', $updatedTask->title);
        $this->assertDatabaseHas('tasks', ['title' => 'New Title']);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();

        $result = $this->taskRepository->delete($task->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
