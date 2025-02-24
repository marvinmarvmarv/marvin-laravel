<?php

namespace Tests\Feature\RepositoryTests;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectRepository = new ProjectRepository(new Project);
    }

    public function test_can_create_project()
    {
        $projectData = Project::factory()->make([
            'name' => 'testProject',
            'description' => 'some description',
        ])->toArray();

        $project = $this->projectRepository->create($projectData);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'testProject',
        ]);
    }

    public function test_can_get_project()
    {
        $project = Project::factory()->create();

        $foundProject = $this->projectRepository->findById($project->id);

        $this->assertEquals($project->id, $foundProject->id);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_can_update_project()
    {
        $project = Project::factory()->create([
            'name' => 'Old Project Name',
        ]);

        $updatedProject = $this->projectRepository->update($project->id, [
            'name' => 'New Project Name',
        ]);

        $this->assertEquals('New Project Name', $updatedProject->name);
        $this->assertDatabaseHas('projects', [
            'name' => 'New Project Name',
        ]);
    }

    public function test_can_delete_project()
    {
        $project = Project::factory()->create();

        $result = $this->projectRepository->delete($project->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }
}
