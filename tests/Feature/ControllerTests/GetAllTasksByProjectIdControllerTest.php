<?php

namespace Tests\Feature\ControllerTests;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllTasksByProjectIdControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_all_tasks_by_project_id()
    {
        $admin = User::factory()->create([
            'name' => 'tester',
            'role' => UserRole::ADMIN->value,
        ]);

        $project = Project::factory()->create();
        $task1 = Task::factory()->create(['user_id' => $admin->id, 'title' => 'Task One', 'project_id' => $project->id]);
        $task2 = Task::factory()->create(['user_id' => $admin->id, 'title' => 'Task Two', 'project_id' => $project->id]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/projects/'.$project->id.'/tasks');

        $response->assertStatus(200);
        $responseData = $response->json();
        foreach ($responseData as $taskData) {
            $this->assertEquals($project->id, $taskData['project_id']);
        }
    }
}
