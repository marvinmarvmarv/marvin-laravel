<?php

namespace Tests\Unit\ServiceTests;

use App\Enums\UserRole;
use App\Mappers\UserMapper;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\User\UserService;
use Illuminate\Support\Collection;
use Mockery as m;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private m\MockInterface|UserRepositoryInterface $mockRepo;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var UserRepositoryInterface|MockInterface */
        $this->mockRepo = m::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->mockRepo);
    }

    public function test_can_get_all_users()
    {
        $usersCollection = collect([
            User::factory()->make(['id' => 1]),
            User::factory()->make(['id' => 2]),
        ]);
        $this->mockRepo
            ->shouldReceive('all')
            ->once()
            ->andReturn($usersCollection);

        $result = $this->userService->getAllUsers();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals(UserMapper::toDTO($usersCollection[0]->toArray()), $result->get(0));
        $this->assertEquals(UserMapper::toDTO($usersCollection[1]->toArray()), $result->get(1));
    }

    public function test_can_create_user()
    {
        $data = User::factory()->make(['id' => 1])->toArray();
        $user = User::factory()->make($data);

        $this->mockRepo
            ->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($user);

        $result = $this->userService->createUser($data);
        $expected = UserMapper::toDTO($user->toArray());

        $this->assertEquals($expected, $result);
    }

    public function test_can_get_user()
    {
        $user = User::factory()->make(['id' => 1]);

        $this->mockRepo
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $result = $this->userService->getUser(1);
        $expected = UserMapper::toDTO($user->toArray());

        $this->assertEquals($expected, $result);
    }

    public function test_can_update_user()
    {
        $data = ['name' => 'Updated Name', 'email' => 'example@test.de', 'password' => 'secretxy', 'role' => UserRole::USER->value];

        $user = User::factory()->make(['id' => 1]);

        $this->mockRepo
            ->shouldReceive('update')
            ->with(1, $data)
            ->once()
            ->andReturnUsing(function ($id, $updateData) use ($user) {
                $user->fill($updateData);

                return $user;
            });

        $result = $this->userService->updateUser(1, $data);
        $expected = UserMapper::toDTO($user->toArray());

        $this->assertEquals($expected, $result);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->make(['id' => 1]);

        $this->mockRepo
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->mockRepo
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $result = $this->userService->deleteUser(1);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
