<?php

namespace Tests\Feature\ControllerTests;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use App\Services\Project\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_index_projects()
    {
        $perPage = 10;
        $requestParams = ['per_page' => $perPage];

        $data = [
            ['id' => 1, 'name' => 'Project 1', 'description' => 'Description 1'],
            ['id' => 2, 'name' => 'Project 2', 'description' => 'Description 2'],
        ];

        $paginator = new LengthAwarePaginator(
            $data,
            count($data),
            $perPage,
            1,
            ['path' => '/api/projects']
        );

        $projectServiceMock = Mockery::mock(ProjectService::class);
        $projectServiceMock->shouldReceive('getAllProjectsWithPipeline')
            ->once()
            ->with($requestParams, $perPage)
            ->andReturn($paginator);

        $this->app->instance(ProjectService::class, $projectServiceMock);

        $adminUser = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($adminUser, 'sanctum')
            ->getJson('/api/admin/projects?per_page='.$perPage);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => count($data),
            ])
            ->assertJsonFragment([
                'name' => 'Project 1',
            ])
            ->assertJsonFragment([
                'name' => 'Project 2',
            ]);
    }

    public function test_admin_can_store_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $projectData = [
            'name' => 'Integration Project',
            'description' => 'Integration description',
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/projects', $projectData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Integration Project',
                'description' => 'Integration description',
            ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Integration Project',
        ]);
    }

    public function test_admin_can_show_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create([
            'name' => 'Show Project',
            'description' => 'Project for show test',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/projects/'.$project->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $project->id,
                'name' => 'Show Project',
            ]);
    }

    public function test_admin_can_update_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create([
            'name' => 'Old Project Name',
            'description' => 'Old description',
        ]);

        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/admin/projects/'.$project->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Project Name',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
        ]);
    }

    public function test_admin_can_delete_project()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $project = Project::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/admin/projects/'.$project->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_user_cannot_index_projects()
    {
        $perPage = 10;
        $requestParams = ['per_page' => $perPage];

        $data = [
            ['id' => 1, 'name' => 'Project 1', 'description' => 'Description 1'],
            ['id' => 2, 'name' => 'Project 2', 'description' => 'Description 2'],
        ];

        $paginator = new LengthAwarePaginator(
            $data,
            count($data),
            $perPage,
            1,
            ['path' => '/api/projects']
        );

        $projectServiceMock = Mockery::mock(ProjectService::class);
        $projectServiceMock->shouldNotReceive('getAllProjectsWithPipeline');
        $this->app->instance(ProjectService::class, $projectServiceMock);

        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/projects?per_page='.$perPage);

        $response->assertStatus(403);
    }

    public function test_user_cannot_show_project()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $project = Project::factory()->create([
            'name' => 'Show Project',
            'description' => 'Project for show test',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/projects/'.$project->id);

        $response->assertStatus(403);
    }

    public function test_user_cannot_store_project()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $projectData = [
            'name' => 'Integration Project',
            'description' => 'Integration description',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/admin/projects', $projectData);

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_project()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $project = Project::factory()->create([
            'name' => 'Old Project Name',
            'description' => 'Old description',
        ]);

        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/admin/projects/'.$project->id, $updateData);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_project()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $project = Project::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/admin/projects/'.$project->id);

        $response->assertStatus(403);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
