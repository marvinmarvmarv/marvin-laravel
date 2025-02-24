<?php

namespace Tests\Feature\ControllerTests;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllTasksByUserIdControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_all_own_tasks_by_id()
    {
        $user = User::factory()->create([
            'name' => 'tester',
            'role' => UserRole::USER->value,
        ]);

        $task1 = Task::factory()->create(['user_id' => $user->id, 'title' => 'Task One']);
        $task2 = Task::factory()->create(['user_id' => $user->id, 'title' => 'Task Two']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users/'.$user->id.'/tasks');

        $response->assertStatus(200);
        $responseData = $response->json();
        foreach ($responseData as $taskData) {
            $this->assertEquals($user->id, $taskData['user_id']);
        }
    }
}
