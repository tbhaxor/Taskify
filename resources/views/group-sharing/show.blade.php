@extends('layouts.base')

@section('title', 'User Permissions on Group')

@section('content')
    <div class="card row mb-6">
        <div class="card-body">
            <h3 class="card-title">User Permissions of {{ $userGroupRole->user->email }} on {{ $group->title }}</h3>
            <small class="card-text">The user email {{ $userGroupRole->user->email  }} has following permissions on
                the {{ $group->title }} group.</small>
            <ul>
                @foreach($userGroupRole->role->permissions as $permission)
                    <li>{{$permission->value->description()}}</li>
                @endforeach
            </ul>

            <a href="{{ route('group-sharing.index', ['group' => $group]) }}" class="card-link">All Roles</a>
            <a href="{{ route('group-sharing.edit', ['group' => $group, 'userGroupRole' => $userGroupRole]) }}"
               class="card-link">Edit</a>
            <a href="{{ route('group-sharing.delete', ['group' => $group, 'userGroupRole' => $userGroupRole]) }}"
               class="card-link text-danger">Delete</a>
        </div>
    </div>
@endsection
