<?php

namespace Tests\Unit\ServiceTests;

use App\DTO\TaskDTO;
use App\Enums\TaskStatus;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\User\UserQueryService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery as m;
use Tests\TestCase;

class UserQueryServiceTest extends TestCase
{
    private $mockRepo;

    private UserQueryService $userQueryService;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var m\MockInterface|UserRepositoryInterface */
        $this->mockRepo = m::mock(UserRepositoryInterface::class);
        $this->userQueryService = new UserQueryService($this->mockRepo);
    }

    public function test_can_get_all_tasks_by_user_id(): void
    {
        $userId = 1;

        $taskData = [
            'id' => 123,
            'title' => 'Test Task',
            'description' => 'Task description',
            'status' => TaskStatus::TODO->value,
            'deadline' => Carbon::tomorrow(),
            'user_id' => $userId,
        ];

        $taskMock = m::mock();
        $taskMock->shouldReceive('toArray')
            ->andReturn($taskData);

        $collection = new Collection([$taskMock]);

        $this->mockRepo->shouldReceive('findAllTasksByUserId')
            ->once()
            ->with($userId)
            ->andReturn($collection);

        $result = $this->userQueryService->findAllTasksByUserId($userId);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertNotEmpty($result);

        $firstTaskDTO = $result->first();
        $this->assertInstanceOf(TaskDTO::class, $firstTaskDTO);
        $this->assertEquals($taskData['id'], $firstTaskDTO->id);
        $this->assertEquals($taskData['title'], $firstTaskDTO->title);
        $this->assertEquals($taskData['description'], $firstTaskDTO->description);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
