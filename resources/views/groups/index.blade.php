@use('App\Enums\UserPermission')
@extends('layouts.base')

@section('title', 'All Groups')

@section('content')
    <h1>List of All Groups</h1>
    <div id="alert-container"></div>
    <a href="{{ route('group.create' )}}" class="btn btn-primary">Create New</a>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Tasks Count</th>
            <th scope="col">Last Updated At</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($groups as $group)
            <tr>
                <th scope="row">{{ $loop->index + 1 }}</th>
                <td><a href="{{ route('group.show', ['group' => $group]) }}">{{ $group->title }}</a></td>
                <td>{{ $group->tasks_count }}</td>
                <td>{{ $group->updated_at }}</td>
                <td>
                    <div class="btn-group" role="group">
                        @can(UserPermission::EDIT_GROUPS->value, $group)
                            <a href="{{ route('group.edit', ['group' => $group]) }}"
                               class="btn btn-sm btn-primary">Edit</a>
                        @endcan
                        @can(UserPermission::DELETE_GROUPS->value, $group)
                            <a href="{{ route('group.delete', ['group' => $group]) }}"
                               class="btn btn-sm btn-danger">Delete</a>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @vite('resources/js/message-handler.js')
@endsection
