<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index()
    {
        return view('tasks.index', [
            'tasks' => Task::all(),
        ]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('tasks.create');
        }

        Task::create($request->validate([
            'title' => 'required|max:256',
            'description' => '',
        ]));

        return to_route('task.index', [
            'message' => 'New task has been created',
        ]);
    }

    public function show(Task $task)
    {
        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    public function edit(Request $request, Task $task)
    {
        if ($request->isMethod('GET')) {
            return view('tasks.edit', [
                'task' => $task,
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:256',
            'description' => '',
            'status' => 'in:pending,in-progress,completed',
        ]);

        if ($task->status != TaskStatus::Completed && $validated['status'] == 'completed') {
            $task->completed_at = Carbon::now();
        }

        if ($task->status == TaskStatus::Completed && $validated['status'] != 'completed') {
            throw ValidationException::withMessages([
                'status' => 'Changing task status is not allowed once it is marked as completed.',
            ]);
        }

        $task->update($validated);

        return to_route('task.show', [
            'task' => $task,
        ]);
    }

    public function delete(Task $task)
    {
        if (request()->isMethod('GET')) {
            return view('tasks.delete', [
                'task' => $task,
            ]);
        }

        $task->delete();

        return to_route('task.index', [
            'message' => 'Task is deleted.',
        ]);
    }
}
