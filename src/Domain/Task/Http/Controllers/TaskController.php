<?php

namespace Domain\Task\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Task\Models\Task;
use Domain\Task\Repositories\TaskRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

        return view('task::index', [
            'tasks'          => $this->taskRepo->getFilteredTasksForUser($request->user()->id(), $filters),
            'STATUS'         => Task::STATUS, // Still using constant for UI
            'filter_status'  => $filters['status'],
            'filter_created' => $filters['created'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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

        $this->taskRepo->createForUser($request->user()->id(), $validated);

        return redirect(route('tasks.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('task::edit', [
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
