@extends('layouts.base')

@section('title', 'Create Task')

@section('content')
    <h1>Create Task</h1>

    <form action="" method="post">
        @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="title" class="form-control @error('title') is-invalid @enderror" required id="title" name="title" placeholder="Enter the task title....">
                @error('title')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="20" placeholder="(Optional) Enter the task description...."></textarea>
                @error('description')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection