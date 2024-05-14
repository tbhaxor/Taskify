@extends('layouts.base')

@section('title', 'Edit Group')

@section('content')
    <h1>Edit Group <strong>{{ $group->title }}</strong></h1>

    <form action="" method="post">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control @error('title') is-invalid  @enderror" required value="{{ old('title', $group->title) }}" id="title" name="title" placeholder="Enter the group title....">
            @error('title')
                <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" id="description" rows="20" placeholder="(Optional) Enter the group description....">{{ old('description', $group->description) }}</textarea>
            @error('description')
                <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>
            
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection