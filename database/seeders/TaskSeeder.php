<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();

        foreach ($users as $user) {
            Task::factory(rand(1, 50))->create([
                'user_id' => $user->id,
                'project_id' => $projects->isNotEmpty() && rand(0, 1)
                    ? $projects->random()->id
                    : null,
                'status' => collect(TaskStatus::cases())->random()->value,
                'deadline' => rand(0, 100) < 70
                    ? now()->addDays(rand(1, 30))
                    : now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
