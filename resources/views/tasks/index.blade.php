@use('App\Enums\UserPermission')

<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Title</th>
        <th scope="col">Status</th>
        <th scope="col">Last Updated At</th>
        <th scope="col">Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($tasks as $task)
        <tr>
            <th scope="row">{{ $loop->index + 1 }}</th>
            <td><a href="{{ route('task.show', ['task' => $task, 'group' => $group]) }}">{{ $task->title }}</a></td>
            <td>{{ $task->status }}</td>
            <td>{{ $task->updated_at }}</td>
            <td>
                <div class="btn-group" role="group">
                    @can(UserPermission::EDIT_TASKS->value, $group)
                        <a href="{{ route('task.edit', ['task' => $task, 'group' => $group]) }}"
                           class="btn btn-sm btn-primary">Edit</a>
                    @endcan
                    @can(UserPermission::DELETE_TASKS->value, $group)
                        <a href="{{ route('task.delete', ['task' => $task, 'group' => $group]) }}"
                           class="btn btn-sm btn-danger">Delete</a>
                    @endcan
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
