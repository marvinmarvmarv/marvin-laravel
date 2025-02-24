<?php

namespace Tests\Feature\ControllerTests;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllExpiredTasksControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_all_expired_tasks()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $expiredTask1 = Task::factory()->create([
            'deadline' => Carbon::now()->subDays(5),
        ]);
        $expiredTask2 = Task::factory()->create([
            'deadline' => Carbon::now()->subDays(1),
        ]);
        $nonExpiredTask = Task::factory()->create([
            'deadline' => Carbon::now()->addDays(2),
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/tasks/all/expired');

        $response->assertStatus(200);

        $responseData = $response->json();

        $returnedIds = collect($responseData)->pluck('id')->all();
        $this->assertContains($expiredTask1->id, $returnedIds);
        $this->assertContains($expiredTask2->id, $returnedIds);
        $this->assertNotContains($nonExpiredTask->id, $returnedIds);
    }

    public function test_user_cannot_get_all_expired_tasks()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $expiredTask1 = Task::factory()->create([
            'deadline' => Carbon::now()->subDays(5),
        ]);
        $expiredTask2 = Task::factory()->create([
            'deadline' => Carbon::now()->subDays(1),
        ]);
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/tasks/all/expired');

        $response->assertStatus(403);
    }
}
