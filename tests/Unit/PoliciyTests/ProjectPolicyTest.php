<?php

namespace Tests\Unit\PolicyTests;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    public function test_admin_can_view_all_projects()
    {
        $policy = new ProjectPolicy;

        $admin = new User([
            'id' => 1,
            'role' => UserRole::ADMIN->value,
        ]);

        $project = new Project([
            'id' => 99,
            'name' => 'Some Project',
            'description' => 'make sth funny',
        ]);

        $project->setRelation('tasks', collect([]));
        $this->assertTrue($policy->view($admin, $project));
    }

    public function test_admin_can_create_project()
    {
        $policy = new ProjectPolicy;

        $admin = new User(['role' => UserRole::ADMIN->value]);

        $this->assertTrue($policy->create($admin));
    }

    public function test_admin_can_update_project()
    {
        $policy = new ProjectPolicy;

        $admin = new User(['role' => UserRole::ADMIN->value]);
        $project = new Project(['id' => 101]);

        $this->assertTrue($policy->update($admin, $project));
    }

    public function test_admin_can_delete_project()
    {
        $policy = new ProjectPolicy;

        $admin = new User(['role' => UserRole::ADMIN->value]);
        $project = new Project(['id' => 101]);

        $this->assertTrue($policy->delete($admin, $project));
    }

    public function test_user_cannot_view_all_projects()
    {
        $policy = new ProjectPolicy;

        $user = new User([
            'id' => 2,
            'role' => UserRole::USER->value,
        ]);

        $project = new Project([
            'id' => 99,
            'name' => 'Some Project',
            'description' => 'make sth funny',
        ]);

        $project->setRelation('tasks', collect([]));
        $this->assertFalse($policy->view($user, $project));
    }

    public function test_user_cannot_create_project()
    {
        $policy = new ProjectPolicy;

        $user = new User(['role' => UserRole::USER->value]);
        $project = new Project(['id' => 102]);

        $this->assertFalse($policy->create($user, $project));
    }

    public function test_user_cannot_update_project()
    {
        $policy = new ProjectPolicy;

        $user = new User(['role' => UserRole::USER->value]);
        $project = new Project(['id' => 102]);

        $this->assertFalse($policy->update($user, $project));
    }

    public function test_user_cannot_delete_project()
    {
        $policy = new ProjectPolicy;

        $user = new User(['role' => UserRole::USER->value]);
        $project = new Project(['id' => 103]);

        $this->assertFalse($policy->delete($user, $project));
    }

    public function test_user_can_view_project_with_mapped_tasks()
    {
        $user = new User([
            'id' => 3,
            'role' => UserRole::USER->value,
        ]);

        $task = new Task([
            'id' => 1,
            'user_id' => $user->id,
            'title' => 'Beispiel',
        ]);

        $project = new Project([
            'id' => 100,
            'name' => 'Test Project',
        ]);
        $project->setRelation('tasks', collect([$task]));

        $policy = new ProjectPolicy;
        $this->assertTrue($policy->view($user, $project));
    }

    public function test_user_cannot_view_project_without_mapped_tasks()
    {
        $policy = new ProjectPolicy;

        $user = new User([
            'id' => 3,
            'role' => UserRole::USER->value,
        ]);

        $project = new Project([
            'id' => 100,
            'name' => 'Another Project',
            'description' => 'make sth think',
        ]);

        $project->setRelation('tasks', collect([]));
        $this->assertFalse($policy->view($user, $project));
    }
}
