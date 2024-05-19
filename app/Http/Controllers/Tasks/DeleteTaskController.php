<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\DeleteTaskRequest;
use App\Models\Group;
use App\Models\Task;

class DeleteTaskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteTaskRequest $request, Group $group, Task $task)
    {
        if ($request->isMethod('GET')) {
            return view('tasks.delete', [
                'group' => $group,
                'task' => $task
            ]);
        }

        $task->delete();

        return to_route('group.show', [
            'group' => $group,
            'message' => 'Task is deleted.'
        ]);
    }
}
