<?php

namespace Tests\Feature\RepositoryTests;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository(new User);
    }

    public function test_can_create_user()
    {
        $userData = User::factory()->make([
            'name' => 'tester',
            'email' => 'tester@qtest.de',
            'password' => 'secretxy',
            'role' => UserRole::USER->value,
        ])->toArray();

        $user = $this->userRepository->create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    public function test_can_get_user()
    {
        $user = User::factory()->create();

        $foundUser = $this->userRepository->findById($user->id);

        $this->assertEquals($user->id, $foundUser->id);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $updatedUser = $this->userRepository->update($user->id, [
            'name' => 'New Name',
        ]);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertDatabaseHas('users', ['name' => 'New Name']);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $result = $this->userRepository->delete($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
