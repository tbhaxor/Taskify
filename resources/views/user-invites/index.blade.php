@extends('layouts.base')


@section('title', 'User Invites')

@section('content')
    <h1>User Invitations for {{ $group->title }}</h1>
    <div id="alert-container"></div>
    <a href="{{ route('user-invite.create', ['group' => $group] )}}" class="btn btn-primary">Invite User</a>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Email Address</th>
            <th scope="col">Role</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($userInvites as $userInvite)
            <tr>
                <th scope="row">{{ $loop->index + 1 }}</th>
                <td>
                    <a href="mailto:{{$userInvite->email}}">{{ $userInvite->email }}</a>
                </td>
                <td><a href="{{ route('role.show', ['role' => $userInvite->role]) }}">{{ $userInvite->role->name }}</a>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('user-invite.edit', ['group' => $group, 'userInvite' => $userInvite]) }}"
                           class="btn btn-sm btn-primary">Edit</a>
                        <a href="{{ route('user-invite.delete', ['group' => $group, 'userInvite' => $userInvite]) }}"
                           class="btn btn-sm btn-danger">Delete</a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @vite('resources/js/message-handler.js')
@endsection
