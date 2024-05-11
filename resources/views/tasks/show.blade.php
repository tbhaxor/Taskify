@extends('layouts.base')

@section('title', 'Show Task')

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
            <strong>Updated At:</strong>
            {{ $task->updated_at }}
        </p>
      
        <a href="{{ route('task.index') }}" class="card-link">All Tasks</a>
        <a href="{{ route('task.edit', ['task' => $task]) }}" class="card-link">Edit</a>
        <a href="{{ route('task.delete', ['task' => $task]) }}" class="card-link text-danger">Delete</a>
    </div>
  </div>
@endsection