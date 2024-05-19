<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\CreateTaskRequest;
use App\Models\Group;

class CreateTaskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateTaskRequest $request, Group $group)
    {
        if ($request->isMethod('GET')) {
            return view('tasks.create');
        }

        $group->tasks()->create($request->safe(['title', 'description']));

        return to_route('group.show', [
            'group' => $group,
            'message' => 'New task has been created.',
        ]);
    }
}
