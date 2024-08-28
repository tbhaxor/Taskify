@extends('layouts.base')

@section('title', 'Edit Group Invite')

@section('content')
    <h1>Edit Group Invite for {{$userInvite->email}}</h1>

    <form action="" method="post">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input disabled value="{{ old('email', $userInvite->email) }}" type="email" class="form-control" id="email"
                   required name="email" placeholder="Enter the email address....">
        </div>
        <div class="mb-3">
            <label for="role_id" class="form-label">Select Role</label>
            <select id="role_id" name="role_id" class="form-select @error('email') is-error @enderror">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        @selected(old('role_id', $userInvite->role_id) === $role->id)>{{$role->name}}</option>
                @endforeach
            </select>
            @error('role_id')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
