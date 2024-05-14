@extends('layouts.base')

@section('title', 'Create Group')

@section('content')
    <h1>Create Group</h1>

    <form action="" method="post">
        @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="title" class="form-control @error('title') is-error @enderror" id="title" required name="title" placeholder="Enter the group title....">
                @error('title')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="20" placeholder="(Optional) Enter the group description...."></textarea>
                @error('description')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection