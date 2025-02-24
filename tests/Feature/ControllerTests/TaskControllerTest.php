<?php

namespace Tests\Feature\ControllerTests;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use App\Services\Task\TaskService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_index_tasks()
    {
        $perPage = 15;
        $requestParams = ['per_page' => $perPage];

        $data = [
            ['id' => 1, 'title' => 'Task 1'],
            ['id' => 2, 'title' => 'Task 2'],
        ];

        $paginator = new LengthAwarePaginator(
            $data,
            count($data),
            $perPage,
            1,
            ['path' => '/api/tasks/all']
        );

        $taskServiceMock = Mockery::mock(TaskService::class);
        $taskServiceMock->shouldReceive('getAllTasksWithPipeline')
            ->once()
            ->with($requestParams, $perPage)
            ->andReturn($paginator);

        $this->app->instance(TaskService::class, $taskServiceMock);

        $adminUser = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $response = $this->actingAs($adminUser, 'sanctum')
            ->getJson('/api/admin/tasks?per_page='.$perPage);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => count($data),
            ])
            ->assertJsonFragment([
                'id' => 1,
                'title' => 'Task 1',
            ]);
    }

    public function test_admin_can_store_task()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test description',
            'status' => 'todo',
            'deadline' => '2025-03-03',
            'user_id' => $admin->id,
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Task',
                'description' => 'Test description',
                'status' => 'todo',
                'user_id' => $admin->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_show_other_users_task()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Show Task',
            'description' => 'Show description',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/tasks/'.$task->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => 'Show Task',
            ]);
    }

    public function test_admin_can_update_other_users_task_if_expired()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $task = Task::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Old Title',
            'description' => 'Old description',
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'New description',
            'user_id' => $admin->id,
            'status' => 'todo',
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/tasks/'.$task->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Title',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_admin_can_delete_other_users_task_if_expired()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $task = Task::factory()->create([
            'user_id' => $admin->id,
            'deadline' => Carbon::now()->subDays(10),
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/tasks/'.$task->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_index_tasks()
    {
        $perPage = 15;
        $normalUser = User::factory()->create(['role' => UserRole::USER->value]);

        $response = $this->actingAs($normalUser, 'sanctum')
            ->getJson('/api/admin/tasks');

        $response->assertStatus(403);
    }

    public function test_user_can_store_task()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test description',
            'status' => 'todo',
            'deadline' => '2025-03-03',
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Task',
                'description' => 'Test description',
                'status' => 'todo',
                'user_id' => $user->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_show_own_task()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Show Task',
            'description' => 'Show description',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/tasks/'.$task->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => 'Show Task',
            ]);
    }

    public function test_user_cannot_show_other_users_task()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $otherUser = User::factory()->create(['role' => UserRole::USER->value]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Show Task',
            'description' => 'Show description',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/tasks/'.$task->id);

        $response->assertStatus(403);
    }

    public function test_user_can_update_own_task_if_not_expired()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'description' => 'Old description',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'New description',
            'user_id' => $user->id,
            'status' => 'todo',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/tasks/'.$task->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Title',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_user_cannot_update_other_users_task()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $otherUser = User::factory()->create(['role' => UserRole::USER->value]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Old Title',
            'description' => 'Old description',
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'New description',
            'user_id' => $otherUser->id,
            'status' => 'todo',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/tasks/'.$task->id, $updateData);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_task_if_not_expired()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'deadline' => Carbon::now()->addDays(10),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/tasks/'.$task->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_delete_other_users_task()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $otherUser = User::factory()->create(['role' => UserRole::USER->value]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'deadline' => Carbon::now()->subDays(10),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/tasks/'.$task->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
