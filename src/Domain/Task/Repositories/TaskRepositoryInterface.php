<?php
namespace Domain\Task\Repositories;

use Domain\Task\Models\Task;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     * @return Collection<int, Task>
     */
    public function getFilteredTasksForUser(int $userId, array $filters): Collection;
    /**
     * @param array<string, mixed> $data
     */
    public function createForUser(int $userId, array $data): Task;
    /**
     * @param array<string, mixed> $data
     */
    public function update(Task $task, array $data): bool;
    public function delete(Task $task): bool;
}