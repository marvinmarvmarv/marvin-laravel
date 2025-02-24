<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    public function test_admin_can_view_all_users()
    {
        $policy = new UserPolicy;

        $admin = new User([
            'id' => 1,
            'role' => UserRole::ADMIN->value,
        ]);

        $this->assertTrue($policy->viewAny($admin));
    }

    public function test_user_cannot_view_all_users()
    {
        $policy = new UserPolicy;

        $user = new User([
            'id' => 2,
            'role' => UserRole::USER->value,
        ]);

        $this->assertFalse($policy->viewAny($user));
    }

    public function test_user_can_view_self()
    {
        $policy = new UserPolicy;

        $user = new User([
            'id' => 3,
            'role' => UserRole::USER->value,
        ]);

        $this->assertTrue($policy->view($user, $user));
    }

    public function test_user_cannot_view_another_user()
    {
        $policy = new UserPolicy;

        $user = new User([
            'id' => 3,
            'role' => UserRole::USER->value,
        ]);

        $otherUser = new User([
            'id' => 4,
            'role' => UserRole::USER->value,
        ]);

        $this->assertFalse($policy->view($user, $otherUser));
    }

    public function test_user_can_update_self()
    {
        $policy = new UserPolicy;

        $user = new User([
            'id' => 5,
            'role' => UserRole::USER->value,
        ]);

        $this->assertTrue($policy->update($user, $user));
    }

    public function test_user_cannot_update_another_user()
    {
        $policy = new UserPolicy;

        $user = new User([
            'id' => 5,
            'role' => UserRole::USER->value,
        ]);

        $otherUser = new User([
            'id' => 6,
            'role' => UserRole::USER->value,
        ]);

        $this->assertFalse($policy->update($user, $otherUser));
    }

    public function test_user_can_delete_self()
    {
        $policy = new UserPolicy;

        $user = new User([
            'id' => 7,
            'role' => UserRole::USER->value,
        ]);

        $this->assertTrue($policy->delete($user, $user));
    }

    public function test_user_cannot_delete_another_user()
    {
        $policy = new UserPolicy;

        $user = new User([
            'id' => 7,
            'role' => UserRole::USER->value,
        ]);

        $otherUser = new User([
            'id' => 8,
            'role' => UserRole::USER->value,
        ]);

        $this->assertFalse($policy->delete($user, $otherUser));
    }
}
