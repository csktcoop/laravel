<?php

namespace Domain\Task;

use Illuminate\Support\ServiceProvider;
use Domain\Task\Repositories\TaskRepositoryInterface;
use Domain\Task\Repositories\TaskRepository;

class TaskServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 1. Bind the Repository
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
    }

    public function boot(): void
    {
        // 2. Load Views with a "Namespace"
        // This tells Laravel: "When I ask for 'task::index', look in this folder."
        $this->loadViewsFrom(__DIR__.'/Resources/views', 'task');
    }
}