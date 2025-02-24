<?php

namespace Tests\Unit\ServiceTests;

use App\DTO\ProjectDTO;
use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\Project\ProjectService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Mockery as m;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    private m\MockInterface|ProjectRepositoryInterface $mockRepo;

    private ProjectService $projectService;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var ProjectRepositoryInterface|MockInterface */
        $this->mockRepo = m::mock(ProjectRepositoryInterface::class);
        $this->projectService = new ProjectService($this->mockRepo);
    }

    public function test_can_get_all_projects()
    {
        app()->instance(Pipeline::class, new class
        {
            protected $payload;

            public function send($payload)
            {
                $this->payload = $payload;

                return $this;
            }

            public function through(array $filters)
            {
                return $this;
            }

            public function thenReturn()
            {
                return is_array($this->payload) ? $this->payload : [$this->payload];
            }
        });

        $project1 = Project::factory()->make([
            'id' => 1,
            'name' => 'Project 1',
            'description' => 'Make sth great',
        ]);
        $project2 = Project::factory()->make([
            'id' => 2,
            'name' => 'Project 2',
            'description' => 'Make sth great again',
        ]);
        $projects = collect([$project1, $project2]);

        $queryMock = m::mock(Builder::class);

        $perPage = config('pagination.default_per_page');

        $paginator = new LengthAwarePaginator(
            $projects,
            $projects->count(),
            $perPage,
            1
        );

        $queryMock->shouldReceive('paginate')
            ->once()
            ->with($perPage)
            ->andReturn($paginator);

        $this->mockRepo->shouldReceive('query')
            ->once()
            ->andReturn($queryMock);

        $filters = [];
        $result = $this->projectService->getAllProjectsWithPipeline($filters, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(2, $result->items());
        $this->assertInstanceOf(ProjectDTO::class, $result->first());
        $this->assertEquals('Project 1', $result->first()->name);
    }

    public function test_can_create_project()
    {
        $projectData = [
            'name' => 'New Project',
            'description' => 'Test Desc',
        ];

        $mockProject = Project::factory()->make(array_merge($projectData, [
            'id' => 1,
        ]));

        $this->mockRepo->shouldReceive('create')
            ->once()
            ->with($projectData)
            ->andReturn($mockProject);

        $projectDTO = $this->projectService->createProject($projectData);

        $this->assertEquals('New Project', $projectDTO->name);
        $this->assertEquals('Test Desc', $projectDTO->description);
        $this->assertEquals(1, $projectDTO->id);
    }

    public function test_can_get_project()
    {
        $projectData = [
            'id' => 33,
            'name' => 'Test Project',
            'description' => 'Some Description',
        ];

        $mockProject = Project::factory()->make($projectData);

        $this->mockRepo->shouldReceive('findById')
            ->once()
            ->with(33)
            ->andReturn($mockProject);

        $projectDTO = $this->projectService->getProject(33);

        $this->assertEquals(33, $projectDTO->id);
        $this->assertEquals('Test Project', $projectDTO->name);
    }

    public function test_can_update_project()
    {
        $id = 22;
        $originalData = [
            'id' => $id,
            'name' => 'Old Project',
            'description' => 'Old Desc',
        ];
        $updateData = [
            'id' => $id,
            'name' => 'Updated Project',
            'description' => 'Updated Desc',
        ];

        $mockExistingProject = Project::factory()->make($originalData);
        $this->mockRepo->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($mockExistingProject);

        $this->mockRepo->shouldReceive('update')
            ->once()
            ->with($id, $updateData)
            ->andReturnUsing(function ($id, $data) use ($mockExistingProject) {
                $mockExistingProject->fill($data);

                return $mockExistingProject;
            });

        $mockExistingProject->refresh = function () use ($mockExistingProject, $updateData) {
            $mockExistingProject->fill($updateData);
        };
        $projectDTO = $this->projectService->updateProject($id, $updateData);
        $this->assertInstanceOf(ProjectDTO::class, $projectDTO);
        $this->assertEquals('Updated Project', $projectDTO->name);
        $this->assertEquals('Updated Desc', $projectDTO->description);
    }

    public function test_can_delete_project()
    {
        $mockProject = Project::factory()->make([
            'id' => 44,
            'name' => 'To Delete',
            'description' => 'blub',
        ]);

        $this->mockRepo->shouldReceive('findById')
            ->once()
            ->with(44)
            ->andReturn($mockProject);

        $this->mockRepo->shouldReceive('delete')
            ->once()
            ->with($mockProject->id)
            ->andReturn(true);

        $result = $this->projectService->deleteProject(44);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
