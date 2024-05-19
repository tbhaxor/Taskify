@extends('layouts.base')

@section('title', 'Show Group')

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
<div class="card row mb-6">
    <div class="card-body">
        <h3 class="card-title">{{ $group->title }}</h3>
        <pre class="card-text">{{ $group->description }}</pre>
        <p>
            <strong>Created At:</strong>
            {{ $group->created_at }}
        </p>
        <p>
            <strong>Updated At:</strong>
            {{ $group->updated_at }}
        </p>
      
        <a href="{{ route('group.index') }}" class="card-link">All Groups</a>
        <a href="{{ route('group.edit', ['group' => $group]) }}" class="card-link">Edit</a>
        <a href="{{ route('group.delete', ['group' => $group]) }}" class="card-link text-danger">Delete</a>
    </div>
</div>
<br>
<div class="row">
    <h4>Tasks</h4>
    <div id="alert-container"></div>
    <div class="">
        <a href="{{ route('task.create', ['group' => $group] )}}" class="btn btn-primary">Create New Task</a>
    </div>

    @include('tasks.index', ['tasks' => $group->tasks, 'group' => $group])
</div>
@endsection

@section('scripts')
    @vite('resources/js/message-handler.js')
@endsection