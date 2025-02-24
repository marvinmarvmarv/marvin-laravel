<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(TaskStatus::cases())->value,
            'deadline' => Carbon::parse(fake()->dateTimeBetween('now', '+1 month'))->toDateString(),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
