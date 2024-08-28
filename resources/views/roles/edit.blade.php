@use('App\Enums\UserPermission')

@extends('layouts.base')

@section('title', 'Edit Role')

@section('content')
    <h1>Edit Role <strong>{{ $role->name }}</strong></h1>

    <form action="" method="post">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input value="{{old('name', $role->name)}}" type="text" placeholder="Enter the role name...."
                   class="form-control @error('name') is-error @enderror" id="name" required name="name">
            @error('name')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="permissions" class="form-label">Permissions</label>
            <select multiple size="12" id="permissions" name="permissions[]" class="form-select">
                @foreach(UserPermission::cases() as $permission)
                    <option
                        @selected(old('permissions', $role->permissions->pluck('value'))->contains($permission))
                        value="{{$permission->value}}">{{ Str::apa(Str::replace('_', ' ', $permission->name)) }}</option>
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

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('select').on('mousedown', function(e) {
                e.preventDefault();
                e.target.parentElement.focus();
                e.target.selected = !e.target.selected;
            });
        });
    </script>
@endsection
