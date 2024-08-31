@extends('layouts.base')

@section('title', 'All Groups')

@section('content')
    <h1>List of All Groups</h1>
    <div id="alert-container"></div>
    <a href="{{ route('role.create' )}}" class="btn btn-primary">Create New</a>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Permissions</th>
            <th scope="col">Last Updated At</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>

        @foreach ($roles as $role)
            <tr>
                <th scope="row">{{ $loop->index + 1 }}</th>
                <td><a href="{{ route('role.show', ['role' => $role]) }}">{{ $role->name }}</a></td>
                <td>{{ $role->permissions_count }}</td>
                <td>{{ $role->updated_at }}</td>
                <td>
                    @if($role->user_id)
                        <div class="btn-group" role="group">
                            <a href="{{ route('role.edit', ['role' => $role]) }}"
                               class="btn btn-sm btn-primary">Edit</a>
                            <a href="{{ route('role.delete', ['role' => $role]) }}"
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
