<?php
namespace Domain\Task\Repositories;

use Domain\Task\Models\Task;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function getFilteredTasksForUser(int $userId, array $filters): Collection;
    public function createForUser(int $userId, array $data): Task;
    public function update(Task $task, array $data): bool;
    public function delete(Task $task): bool;
}