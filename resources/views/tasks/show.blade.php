@use('App\Enums\UserPermission')

@extends('layouts.base')

@section('title', 'Show Task')

@section('styles')
    <style>
        pre {
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">{{ $task->title }}</h3>
            <pre class="card-text">{{ $task->description }}</pre>
            <p><strong>Status:</strong> {{ $task->status }}</p>
            @if ($task->status == \App\Enums\TaskStatus::Completed)
                <p>
                    <strong>Completed At:</strong>
                    {{ $task->completed_at }}
                </p>
            @endif
            <p>
                <strong>Created At:</strong>
                {{ $task->created_at }}
            </p>
            <p>
                <strong>Created By:</strong>
                @if($task->user->is(auth()->user()))
                    You
                @else
                    {{$task->user->name}} &lt;{{$task->user->email}}&gt;
                @endif
            </p>
            <p>
                <strong>Updated At:</strong>
                {{ $task->updated_at }}
            </p>

            <a href="{{ route('group.show', ['group' => $group]) }}" class="card-link">All Tasks</a>
            @can(UserPermission::EDIT_TASKS->value, $group)
                <a href="{{ route('task.edit', ['task' => $task, 'group' => $group]) }}" class="card-link">Edit</a>
            @endcan
            @can(UserPermission::DELETE_TASKS->value, $group)
                <a href="{{ route('task.delete', ['task' => $task, 'group' => $group]) }}"
                   class="card-link text-danger">Delete</a>
            @endcan
        </div>
    </div>
@endsection
