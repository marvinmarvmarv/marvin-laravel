<?php

namespace Tests\Unit\ServiceTests;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Task\TaskService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Mockery as m;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    private m\MockInterface|TaskRepositoryInterface $mockRepo;

    private TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var TaskRepositoryInterface|MockInterface */
        $this->mockRepo = m::mock(TaskRepositoryInterface::class);
        $this->taskService = new TaskService($this->mockRepo);
    }

    public function test_can_get_all_tasks()
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

        $task1 = Task::factory()->make([
            'id' => 1,
            'title' => 'Task 1',
            'description' => 'Test 1',
            'status' => 'todo',
            'deadline' => Carbon::tomorrow(),
            'user_id' => 4,
            'project_id' => 2,
        ]);
        $task2 = Task::factory()->make([
            'id' => 2,
            'title' => 'Task 2',
            'description' => 'Test 2',
            'status' => 'in_progress',
            'deadline' => Carbon::tomorrow(),
            'user_id' => 2,
            'project_id' => 4,
        ]);
        $tasks = collect([$task1, $task2]);
        $queryMock = m::mock(Builder::class);

        $perPage = config('pagination.default_per_page');

        $paginator = new LengthAwarePaginator(
            $tasks,
            $tasks->count(),
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
        $result = $this->taskService->getAllTasksWithPipeline($filters, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(2, $result->items());
        $this->assertInstanceOf(TaskDTO::class, $result->first());
        $this->assertEquals('Task 1', $result->first()->title);
    }

    public function test_can_create_task()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Test Desc',
            'status' => 'todo',
            'deadline' => Carbon::tomorrow(),
            'user_id' => 4,
            'project_id' => 2,
        ];

        $mockTask = Task::factory()->make(array_merge($taskData, [
            'id' => 1,
        ]));

        $this->mockRepo->shouldReceive('create')
            ->once()
            ->with($taskData)
            ->andReturn($mockTask);

        $taskDTO = $this->taskService->createTask($taskData);

        $this->assertEquals('New Task', $taskDTO->title);
        $this->assertEquals('todo', $taskDTO->status->value);
        $this->assertEquals(1, $taskDTO->id);
    }

    public function test_can_get_task()
    {
        $mockTask = Task::factory()->make([
            'id' => 42,
            'title' => 'Single Task',
            'description' => 'Single Desc',
            'status' => 'todo',
            'deadline' => Carbon::tomorrow(),
            'user_id' => 3,
        ]);

        $this->mockRepo->shouldReceive('findById')
            ->once()
            ->with(42)
            ->andReturn($mockTask);

        $taskDTO = $this->taskService->getTask(42);

        $this->assertInstanceOf(TaskDTO::class, $taskDTO);
        $this->assertEquals(42, $taskDTO->id);
        $this->assertEquals('Single Task', $taskDTO->title);
    }

    public function test_can_update_task()
    {
        $id = 42;
        $mockExistingTask = Task::factory()->make([
            'id' => $id,
            'title' => 'Old Task',
            'description' => 'Old Desc',
            'status' => 'todo',
            'deadline' => Carbon::tomorrow(),
            'user_id' => 1,
        ]);

        $updateData = [
            'title' => 'Updated Task',
            'description' => 'Updated Desc',
            'status' => 'in_progress',
            'deadline' => Carbon::tomorrow(),
            'user_id' => 1,
        ];

        $this->mockRepo->shouldReceive('update')
            ->once()
            ->with($id, $updateData)
            ->andReturnUsing(function ($id, $data) use ($mockExistingTask) {
                $mockExistingTask->fill($data);

                return $mockExistingTask;
            });

        $mockExistingTask->refresh = function () use ($mockExistingTask, $updateData) {
            $mockExistingTask->fill($updateData);
        };

        $taskDTO = $this->taskService->updateTask($id, $updateData);

        $this->assertInstanceOf(TaskDTO::class, $taskDTO);
        $this->assertEquals('Updated Task', $taskDTO->title);
        $this->assertEquals('Updated Desc', $taskDTO->description);
        $this->assertEquals('in_progress', $taskDTO->status->value);
    }

    public function test_can_delete_task()
    {
        $mockTask = Task::factory()->make([
            'id' => 999,
            'title' => 'To Delete',
        ]);

        $this->mockRepo->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn($mockTask);

        $this->mockRepo->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(true);

        $result = $this->taskService->deleteTask(999);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
