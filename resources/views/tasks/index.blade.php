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
                <th scope="row">{{ $task->id }}</th>
                <td><a href="{{ route('task.show', ['task' => $task, 'group' => $group]) }}">{{ $task->title }}</a></td>
                <td>{{ $task->status }}</td>
                <td>{{ $task->updated_at }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('task.edit', ['task' => $task, 'group' => $group]) }}" class="btn btn-sm btn-primary">Edit</a>
                        <a href="{{ route('task.delete', ['task' => $task, 'group' => $group]) }}" class="btn btn-sm btn-danger">Delete</a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
