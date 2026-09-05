<?php

namespace DDD\Task\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\User;
use DDD\Task\Models\Task;
use DDD\Task\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Available filter keys for tasks query
     */
    private const ALLOWED_FILTERS = [
        'status' => 'status',
        'assigned_to' => 'assigned_to',
        'title' => 'title',
    ];

    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Get all tasks with optional filters using Strategy pattern.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Task::query();

        // Apply filters using strategy pattern
        foreach (self::ALLOWED_FILTERS as $requestKey => $columnName) {
            if (! $request->filled($requestKey)) {
                continue;
            }

            if ($requestKey === 'title') {
                $query->where($columnName, 'like', '%'.$request->input($requestKey).'%');
                continue;
            }

            $query->where($columnName, $request->input($requestKey));
        }

        // If not admin, only show tasks assigned to the user
        if ($user->role !== 'admin') {
            $query->where('assigned_to', $user->id);
        }

        $tasks = $query->with('user:id,name,email')
            ->select(['id', 'title', 'status', 'due_at']) 
            ->latest()
            ->paginate(20);

        return TaskResource::collection($tasks);
    }

    /**
     * Create a new task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        // canTaskWithoutAssignee
        if (! $validated['assigned_to'] && $user->role !== 'admin') {
            return response()->json([
                'message' => 'Assignee is required',
                'errors' => [
                    'assigned_to' => [
                        'Assignee is required',
                    ],
                ],
            ], 422);
        }

        if (!$this->canUserModifyTask($user, $validated['assigned_to'] ?? null)) {
            return response()->json([
                "message" => "Unauthorized",
                "errors" => [
                    "title" => [
                        "Unauthorized"
                    ]
                ]
            ], 403);
        }

        $task = Task::create($validated);

        return (new TaskResource($task->load('assignee:id,name,email')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get a specific task.
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load('assignee:id,name,email'));
    }

    /**
     * Update a task.
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $validated = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        $task->update($validated);

        return new TaskResource($task->load('assignee:id,name,email'));
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Check if user can modify a task (create or assign to someone).
     * Users can only create/modify tasks for themselves unless they are admin.
     */
    private function canUserModifyTask(User $user, ?int $assignedTo): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $assignedTo === $user->id;
    }

}
