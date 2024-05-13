<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Group;
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

    public function create(Request $request, Group $group)
    {
        if ($request->isMethod('GET')) {
            return view('tasks.create');
        }

        $group->tasks()->create($request->validate([
            'title' => 'required|max:64',
            'description' => '',
        ]));

        return to_route('group.show', [
            'group' => $group,
            'message' => 'New task has been created.',
        ]);
    }

    public function show(Group $group, Task $task)
    {
        if ($group->id != $task->group_id) {
            return to_route('group.show', [
                'group' => $group,
                'error' => 'Requested task does not belong to this group.'
            ]);
        }

        return view('tasks.show', [
            'task' => $task,
            'group' => $group
        ]);
    }

    public function edit(Request $request, Group $group, Task $task)
    {
        if ($group->id != $task->group_id) {
            return to_route('group.show', [
                'group' => $group,
                'error' => 'Requested task does not belong to this group.'
            ]);
        }

        if ($request->isMethod('GET')) {
            return view('tasks.edit', [
                'task' => $task,
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:64',
            'description' => '',
            'status' => 'in:pending,in-progress,completed|required',
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
            'group' => $group,
            'task' => $task,
        ]);
    }

    public function delete(Group $group, Task $task)
    {
        if ($group->id != $task->group_id) {
            return to_route('group.show', [
                'group' => $group,
                'error' => 'Requested task does not belong to this group.'
            ]);
        }

        if (request()->isMethod('GET')) {
            return view('tasks.delete', [
                'task' => $task,
                'group' => $group
            ]);
        }

        $task->delete();

        return to_route('group.show', [
            'group' => $group,
            'message' => 'Task is deleted.',
        ]);
    }
}
