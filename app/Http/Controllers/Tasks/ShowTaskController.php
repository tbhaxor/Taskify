<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\ShowTaskRequest;
use App\Models\Group;
use App\Models\Task;

class ShowTaskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ShowTaskRequest $request, Group $group, Task $task)
    {
        return view('tasks.show', [
            'group' => $group,
            'task' => $task,
        ]);
    }
}
