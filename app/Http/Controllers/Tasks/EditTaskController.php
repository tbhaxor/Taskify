<?php

namespace App\Http\Controllers\Tasks;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\EditTaskRequest;
use App\Models\Group;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class EditTaskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EditTaskRequest $request, Group $group, Task $task)
    {
        if ($request->isMethod('GET')) {
            return view('tasks.edit', [
                'task' => $task
            ]);
        }

        $status = $request->safe()->enum('status', TaskStatus::class);
        if ($task->status == TaskStatus::Completed && $status != TaskStatus::Completed) {
            throw ValidationException::withMessages([
                'status' => 'Changing task status is not allowed once it is marked as completed.',
            ]);
        }

        $payload = collect($request->safe([
            'title',
            'description',
        ]))->merge([
            'status' => $status
        ]);

        if ($task->status != TaskStatus::Completed && $status == TaskStatus::Completed) {
            $payload = $payload->merge([
                'completed_at' => Carbon::now()
            ]);
        }

        $task->update($payload->toArray());

        return to_route('task.show', [
            'group' => $group,
            'task' => $task,
        ]);
    }
}
