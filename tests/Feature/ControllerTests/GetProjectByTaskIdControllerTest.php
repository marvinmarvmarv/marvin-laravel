<?php

namespace Tests\Feature\ControllerTests;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetProjectByTaskIdControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_project_by_task_id()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $project = Project::factory()->create([
            'name' => 'Test Project',
        ]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/projects/task/'.$task->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $project->id,
                'name' => 'Test Project',
            ]);
    }
}
