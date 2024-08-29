@use('App\Models\Role;use App\Models\UserGroupRole')
@extends('layouts.base')

@section('title', 'Group Sharing')

@section('content')
    <h1>List of Associated Users with this Group</h1>
    <div id="alert-container"></div>
    <a href="{{ route('user-invite.create', ['group' => $group] )}}" class="btn btn-primary">Add New User</a>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">User Account</th>
            <th scope="col">Role</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($userGroupRoles as $userGroupRole)
            <tr>
                <th scope="row">{{ $loop->index + 1 }}</th>
                <td>
                    {{ $userGroupRole->user->name }} &lt;{{ $userGroupRole->user->email }}&gt;
                </td>
                <td>
                    <a href="{{ route('role.show', ['role' => $userGroupRole->role ]) }}">{{$userGroupRole->role->name}}</a>
                </td>
                <td>
                    @if($group->user_id !== $userGroupRole->user_id)
                        <div class="btn-group" role="group">
                            <a href="{{ route('group-sharing.edit', ['group' => $group]) . '?user_id=' . $userGroupRole->user->id. '&role_id=' . $userGroupRole->role->id }}"
                               class="btn btn-sm btn-primary">Edit</a>
                            <a href="{{ route('group-sharing.delete', ['group' => $group]) . '?user_id=' . $userGroupRole->user->id. '&role_id=' . $userGroupRole->role->id }}"
                               class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @vite('resources/js/message-handler.js')
@endsection
