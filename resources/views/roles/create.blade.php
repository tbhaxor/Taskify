@use('App\Enums\UserPermission')

@extends('layouts.base')

@section('title', 'Create Role')

@section('content')
    <h1>Create new Role</h1>

    <form action="" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-error @enderror" id="name" required name="name"
                   placeholder="Enter the role name....">
            @error('name')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="permissions" class="form-label">Permissions</label>
            <select multiple size="12" id="permissions" name="permissions[]" class="form-select">
                @foreach(UserPermission::cases() as $permission)
                    <option
                        value="{{$permission->value}}">{{ $permission->description()  }}</option>
                @endforeach
                <option value="erroneous">Erroneous</option>
            </select>
            @error('permissions')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
            @error('permissions.*')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary" type="submit">Save</button>
    </form>
@endsection

