<?php

namespace Tests\Unit\ServiceTests;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Task\TaskQueryService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mockery as m;
use Tests\TestCase;

class TaskQueryServiceTest extends TestCase
{
    private m\MockInterface|TaskRepositoryInterface $mockRepo;

    private TaskQueryService $taskQueryService;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var TaskRepositoryInterface|MockInterface */
        $this->mockRepo = m::mock(TaskRepositoryInterface::class);
        $this->taskQueryService = new TaskQueryService($this->mockRepo);
    }

    public function test_can_get_all_expired_tasks()
    {
        $task1 = new Task(['id' => 1, 'title' => 'Expired A', 'description' => 'test', 'status' => 'todo', 'deadline' => Carbon::yesterday(), 'user_id' => 2]);
        $task2 = new Task(['id' => 2, 'title' => 'Expired B', 'description' => 'test', 'status' => 'in_progress', 'deadline' => Carbon::yesterday(), 'user_id' => 6]);
        $mockExpiredTasks = collect([$task1, $task2]);

        $this->mockRepo->shouldReceive('findAllExpiredTasks')
            ->once()
            ->andReturn($mockExpiredTasks);

        $result = $this->taskQueryService->findAllExpiredTasks();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(TaskDTO::class, $result->first());
        $this->assertEquals(Carbon::yesterday()->toDateString(), $result->first()->deadline->toDateString());
    }
}
