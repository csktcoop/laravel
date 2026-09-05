<?php
namespace DDD\Task\Repositories;

use DDD\Task\Models\Task;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * {inheritdoc}
     */
    public function getFilteredTasksForUser(int $userId, array $filters): Collection
    {
        $query = Task::with('assignee')
            ->where('user_id', $userId)
            ->latest();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['created'])) {
            $query->whereDate('created_at', $filters['created']);
        }

        return $query->get();
    }

    public function createForUser(int $userId, array $data): Task
    {
        // Notice: We don't call $user->tasks(). 
        // We link them via user_id to stay decoupled.
        return Task::create(array_merge($data, ['user_id' => $userId]));
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

}