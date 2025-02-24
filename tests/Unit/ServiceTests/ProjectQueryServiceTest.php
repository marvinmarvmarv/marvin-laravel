<?php

namespace Tests\Unit\ServiceTests;

use App\DTO\ProjectDTO;
use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\Project\ProjectQueryService;
use Mockery as m;
use Tests\TestCase;

class ProjectQueryServiceTest extends TestCase
{
    private m\MockInterface|ProjectRepositoryInterface $mockRepo;

    private ProjectQueryService $projectQueryService;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var ProjectRepositoryInterface|MockInterface */
        $this->mockRepo = m::mock(ProjectRepositoryInterface::class);
        $this->projectQueryService = new ProjectQueryService($this->mockRepo);
    }

    public function test_can_get_project_by_task_id(): void
    {
        $taskId = 99;

        $project = Project::factory()->make([
            'id' => 10,
            'name' => 'Project X',
            'description' => 'Project X description',
        ]);

        $this->mockRepo->shouldReceive('findProjectByTaskId')
            ->once()
            ->with($taskId)
            ->andReturn($project);

        $result = $this->projectQueryService->getProjectByTaskId($taskId);

        $this->assertInstanceOf(ProjectDTO::class, $result);
        $this->assertEquals($project->id, $result->id);
        $this->assertEquals($project->name, $result->name);
        $this->assertEquals($project->description, $result->description);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
