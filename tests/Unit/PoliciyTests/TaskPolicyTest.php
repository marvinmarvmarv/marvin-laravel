<?php

namespace Tests\Unit\PolicyTests;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;
use Carbon\Carbon;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    public function test_admin_can_view_all_tasks()
    {
        $user = new User(['id' => 99, 'role' => UserRole::ADMIN->value]);
        $task = new Task(['id' => 1, 'user_id' => 10]);

        $policy = new TaskPolicy;

        $this->assertTrue($policy->view($user, $task));
    }

    public function test_admin_can_update_other_users_task()
    {
        $user = new User(['id' => 44, 'role' => UserRole::ADMIN->value]);
        $task = new Task(['id' => 1, 'user_id' => 33, 'role' => UserRole::USER->value]);

        $policy = new TaskPolicy;

        $this->assertTrue($policy->update($user, $task));
    }

    public function test_admin_can_delete_other_users_task_if_deadline_has_expired()
    {
        $user = new User(['id' => 44, 'role' => UserRole::ADMIN->value]);
        $task = new Task(['id' => 1, 'user_id' => 33, 'role' => UserRole::USER->value, 'deadline' => Carbon::yesterday()]);

        $policy = new TaskPolicy;

        $this->assertTrue($policy->delete($user, $task));
    }

    public function test_admin_cannot_delete_others_task_if_deadline_has_not_expired()
    {
        $user = new User(['id' => 44, 'role' => UserRole::ADMIN->value]);
        $task = new Task(['id' => 1, 'user_id' => 33, 'role' => UserRole::USER->value, 'deadline' => Carbon::tomorrow()]);

        $policy = new TaskPolicy;

        $this->assertFalse($policy->delete($user, $task));
    }

    public function test_user_can_view_own_task()
    {
        $user = new User(['id' => 10, 'role' => UserRole::USER->value]);
        $task = new Task(['id' => 1, 'user_id' => 10]);

        $policy = new TaskPolicy;

        $this->assertTrue($policy->view($user, $task));
    }

    public function test_user_cannot_view_other_users_task()
    {
        $user = new User(['id' => 10, 'role' => UserRole::USER->value]);
        $task = new Task(['id' => 1, 'user_id' => 20]);

        $policy = new TaskPolicy;

        $this->assertFalse($policy->view($user, $task));
    }

    public function test_user_can_update_own_task()
    {
        $user = new User(['id' => 33, 'role' => UserRole::USER->value]);
        $task = new Task(['id' => 1, 'user_id' => 33, 'role' => UserRole::USER->value]);

        $policy = new TaskPolicy;
        $this->assertTrue($policy->update($user, $task));
    }

    public function test_user_cannot_update_other_users_task()
    {
        $user = new User(['id' => 33, 'role' => UserRole::USER->value]);
        $taskOfOtherUser = new Task(['id' => 2, 'user_id' => 22, 'role' => UserRole::USER->value]);

        $policy = new TaskPolicy;
        $this->assertFalse($policy->update($user, $taskOfOtherUser));
    }

    public function test_user_can_delete_own_task_if_deadline_has_not_expired()
    {
        $user = new User(['id' => 33, 'role' => UserRole::USER->value]);
        $task = new Task(['id' => 1, 'user_id' => 33, 'role' => UserRole::USER->value, 'deadline' => Carbon::tomorrow()]);

        $policy = new TaskPolicy;

        $this->assertTrue($policy->delete($user, $task));
    }

    public function test_user_cannot_delete_own_task_if_deadline_has_expired()
    {
        $user = new User(['id' => 44, 'role' => UserRole::USER->value]);
        $task = new Task(['id' => 1, 'user_id' => 33, 'role' => UserRole::USER->value, 'deadline' => Carbon::yesterday()]);

        $policy = new TaskPolicy;

        $this->assertFalse($policy->delete($user, $task));
    }

    public function test_user_cannot_delete_other_users_task()
    {
        $user = new User(['id' => 44, 'role' => UserRole::USER->value]);
        $task = new Task(['id' => 1, 'user_id' => 33, 'role' => UserRole::USER->value]);

        $policy = new TaskPolicy;

        $this->assertFalse($policy->delete($user, $task));
    }
}
