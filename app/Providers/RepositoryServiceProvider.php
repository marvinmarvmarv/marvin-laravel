<?php

namespace App\Providers;

use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Services\Project\ProjectService;
use App\Services\Task\TaskService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(ProjectRepositoryInterface::class, ProjectRepository::class);

        $this->app->singleton(TaskService::class);
        $this->app->singleton(UserService::class);
        $this->app->singleton(ProjectService::class);
    }

    public function boot(): void
    {
        //
    }
}
