@extends('layouts.base')

@section('title', $role->name)

@section('content')
    <div class="card row mb-6">
        <div class="card-body">
            <h3 class="card-title">{{ $role->name }}</h3>
            <small class="card-text">Users with this role have following permissions:</small>
            <ul>
                @foreach($role->permissions as $permission)
                    <li>{{$permission->value->description()}}</li>
                @endforeach
            </ul>

            <a href="{{ route('role.index') }}" class="card-link">All Roles</a>
            @if ($role->user_id)
                <a href="{{ route('role.edit', ['role' => $role]) }}" class="card-link">Edit</a>
            @endif
            @if ($role->user_id)
                <a href="{{ route('role.delete', ['role' => $role]) }}" class="card-link text-danger">Delete</a>
            @endif
        </div>
    </div>
@endsection
