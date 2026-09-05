<?php

namespace DDD\Task\Http\Controllers\web;

use App\Http\Controllers\Controller;
use DDD\Task\Models\Task;
use DDD\Task\Repositories\TaskRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class TaskController extends Controller
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepo
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status'),
            'created' => $request->input('created'),
        ];

        /** @var \App\Models\User $user */
        $user = $request->user();
        return app('view')->make('task::index', [
            'tasks'          => $this->taskRepo->getFilteredTasksForUser($user->id, $filters),
            'STATUS'         => Task::STATUS, // Still using constant for UI
            'filter_status'  => $filters['status'],
            'filter_created' => $filters['created'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        $this->taskRepo->createForUser($user->id, $validated);

        return redirect(route('tasks.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return app('view')->make('task::edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'status'  => 'required|min:0|max:2',
        ]);

        $this->taskRepo->update($task, $validated);

        return redirect(route('tasks.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
      $this->authorize('delete', $task);
      $this->taskRepo->delete($task);

      return redirect(route('tasks.index'));
    }
}
