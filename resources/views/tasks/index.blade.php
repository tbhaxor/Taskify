@extends('layouts.base')

@section('title', 'All Tasks')

@section('content')
<h1>List of All Tasks</h1>
<a href="{{ route('task.create' )}}" class="btn btn-link">Create New</a>
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
                <td><a href="{{ route('task.show', ['task' => $task]) }}">{{ $task->title }}</a></td>
                <td>{{ $task->status }}</td>
                <td>{{ $task->updated_at }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('task.edit', ['task' => $task]) }}" class="btn btn-sm btn-primary">Edit</a>
                        <a href="{{ route('task.delete', ['task' => $task]) }}" class="btn btn-sm btn-danger">Delete</a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const qp = new URLSearchParams(window.location.search)
            const errorMessage = qp.get('error');
            const infoMessage = qp.get('message');

            if (errorMessage) {
                $('h1').after(`<div class="alert alert-danger" role="alert">${errorMessage}</div>`)
            }
            
            if (infoMessage) {
                $('h1').after(`<div class="alert alert-info" role="alert">${infoMessage}</div>`)
            }
        })
    </script>
@endsection