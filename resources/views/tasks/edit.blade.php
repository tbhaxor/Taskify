@extends('layouts.base')

@section('title', 'Edit Task')

@section('content')
    <h1>Edit Task <strong>{{ $task->title }}</strong></h1>

    <form action="" method="post">
        @csrf
        @method('PUT')
        <div class="mb-3 row">
            <div class="col-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" required value="{{ old('title', $task->title) }}" id="title" name="title" placeholder="Enter the task title....">
                @error('title')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-6">
                <label for="status" class="form-label">Status</label>
                {{ old('status') }}
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="pending" @selected(old('status', $task->status) == \App\Enums\TaskStatus::Pending)>Pending</option>
                    <option value="in-progress" @selected(old('status', $task->status) == \App\Enums\TaskStatus::InProgress)>In-progress</option>
                    <option value="completed" @selected(old('status', $task->status) == \App\Enums\TaskStatus::Completed)>Completed</option>
                    <option value="erroneous" @selected(old('status', $task->status) == 'erroneous')>Erroneous Value</option>
                </select>
                @error('status')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" id="description" rows="20" placeholder="(Optional) Enter the task description....">{{ old('description', $task->description) }}</textarea>
            @error('description')
                <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>
            
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection