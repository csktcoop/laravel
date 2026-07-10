<?php

namespace Domain\Task\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Domain\Task\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Available filter keys for tasks query
     */
    private const ALLOWED_FILTERS = [
        'status' => 'status',
        'assigned_to' => 'assigned_to',
    ];

    /**
     * Get all tasks with optional filters using Strategy pattern.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Task::query();

        // Apply filters using strategy pattern
        foreach (self::ALLOWED_FILTERS as $requestKey => $columnName) {
            if ($request->has($requestKey)) {
                $query->where($columnName, $request->input($requestKey));
            }
        }

        // If not admin, only show tasks assigned to the user
        if ($user->role !== 'admin') {
            $query->where('assigned_to', $user->id);
        }

        $tasks = $query->with('assignee:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($tasks);
    }

    /**
     * Create a new task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        if (!$this->canUserModifyTask($user, $validated['assigned_to'] ?? null)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task = Task::create($validated);

        return response()->json($task->load('assignee:id,name,email'), 201);
    }

    /**
     * Get a specific task.
     */
    public function show(Task $task): JsonResponse
    {
        $user = Auth::user();

        if (!$this->canUserViewTask($user, $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task->load('assignee:id,name,email'));
    }

    /**
     * Update a task.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        if (!$this->canUserViewTask($user, $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update($validated);

        return response()->json($task->load('assignee:id,name,email'));
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $user = Auth::user();

        if (!$this->canUserViewTask($user, $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Check if user can modify a task (create or assign to someone).
     * Users can only create/modify tasks for themselves unless they are admin.
     */
    private function canUserModifyTask($user, ?int $assignedTo): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $assignedTo === $user->id;
    }

    /**
     * Check if user can view/edit/delete a task.
     * Users can only access their own tasks unless they are admin.
     */
    private function canUserViewTask($user, Task $task): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $task->assigned_to === $user->id;
    }
}
