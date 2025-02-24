<?php

namespace Tests\Feature\ControllerTests;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_index_users()
    {
        $usersDTO = collect([
            ['id' => 1, 'name' => 'User One', 'email' => 'userone@example.com'],
            ['id' => 2, 'name' => 'User Two', 'email' => 'usertwo@example.com'],
        ]);

        $userServiceMock = Mockery::mock(UserService::class);
        $userServiceMock->shouldReceive('getAllUsers')
            ->once()
            ->with([])
            ->andReturn($usersDTO);

        $this->app->instance(UserService::class, $userServiceMock);

        $adminUser = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $response = $this->actingAs($adminUser, 'sanctum')
            ->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJson($usersDTO->toArray());
    }

    public function test_admin_can_show_other_user()
    {
        $adminUser = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $userDTO = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        $response = $this->actingAs($adminUser, 'sanctum')
            ->getJson('/api/admin/users/'.$user->id);

        $response->assertStatus(200)
            ->assertJson($userDTO);
    }

    public function test_admin_can_store_other_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'secretxy',
            'role' => 'user',
        ];

        $adminUser = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $response = $this->actingAs($adminUser, 'sanctum')
            ->postJson('/api/admin/users', $userData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'role' => 'user',
            ]);
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_can_update_other_user()
    {
        $adminUser = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'someone@sth.de',
            'password' => 'secretxy',
        ];
        $userDTO = [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => $user->email,
        ];

        $response = $this->actingAs($adminUser, 'sanctum')
            ->putJson('/api/admin/users/'.$user->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Name',
                'email' => 'someone@sth.de',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_other_user()
    {
        $adminUser = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $response = $this->actingAs($adminUser, 'sanctum')
            ->deleteJson('/api/admin/users/'.$user->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_can_show_self()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users/'.$user->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_user_cannot_show_other_user()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $user2 = User::factory()->create(['role' => UserRole::USER->value]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users/'.$user2->id);

        $response->assertStatus(403);
    }

    public function test_user_can_update_self()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email,
            'password' => 'secretxy',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/users/'.$user->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Name',
                'email' => $user->email,
            ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_cannot_update_other_user()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $user2 = User::factory()->create(['role' => UserRole::USER->value]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email,
            'password' => 'secretxy',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/users/'.$user2->id, $updateData);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_self()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/users/'.$user->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_cannot_delete_other_user()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $user2 = User::factory()->create(['role' => UserRole::USER->value]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/users/'.$user2->id);

        $response->assertStatus(403);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
