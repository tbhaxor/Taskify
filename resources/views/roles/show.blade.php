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
            <a href="{{ route('role.edit', ['role' => $role]) }}" class="card-link">Edit</a>
            <a href="{{ route('role.delete', ['role' => $role]) }}" class="card-link text-danger">Delete</a>
            {{--            <table class="table">--}}
            {{--                <thead>--}}
            {{--                <tr>--}}
            {{--                    <th scope="col">#</th>--}}
            {{--                    <th scope="col">User</th>--}}
            {{--                    <th scope="col">Group</th>--}}
            {{--                </tr>--}}
            {{--                </thead>--}}
            {{--                <tbody>--}}
            {{--                @foreach($role->userGroups as $userGroup)--}}
            {{--                    <tr>--}}
            {{--                        <td>--}}
            {{--                            {{$loop->index + 1}}--}}
            {{--                        </td>--}}
            {{--                        <td>--}}
            {{--                            @if($userGroup->user->is(auth()->user()))--}}
            {{--                                You--}}
            {{--                            @else--}}
            {{--                                {{$userGroup->user->name}} ({{$userGroup->user->email}})--}}
            {{--                            @endif--}}
            {{--                        </td>--}}
            {{--                        <td>--}}
            {{--                            <a href="{{ route('group.show', ['group' => $userGroup->group]) }}">{{$userGroup->group->title}}</a>--}}
            {{--                        </td>--}}
            {{--                    </tr>--}}
            {{--                @endforeach--}}
            {{--                </tbody>--}}
            {{--            </table>--}}

        </div>
    </div>

@endsection
