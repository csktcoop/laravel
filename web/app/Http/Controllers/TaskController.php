<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Get items belongs to user.
        $tasks = Task::with('user')
        ->where('user_id', auth()->id())
        ->latest();

        // Filter by attributes.
        $status = $request->get('status');
        if ('0' === $status || $status ) {
            $tasks->where('status', $status);
        }

        $created = $request->get('created');
        if ( $created ) {
            $created_from = "{$created} 00:00:00";
            $created_to   = "{$created} 23:59:59";

            $tasks->whereBetween('created_at', [$created_from, $created_to]);
        }

        return view(
            'tasks.index',
            [
                'tasks'          => $tasks->get(), // For the list.
                'STATUS'         => Task::STATUS,  // For the dropdown.
                'filter_status'  => $status,       // For the filter.
                'filter_created' => $created,      // For the filter.
            ]
        );
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

        $request->user()->tasks()->create($validated);

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

        return view('tasks.edit', [
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

        $task->update($validated);

        return redirect(route('tasks.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
      $this->authorize('delete', $task);

      $task->delete();

      return redirect(route('tasks.index'));
    }
}
