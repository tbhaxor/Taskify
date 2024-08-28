@extends('layouts.base')

@section('title', 'Delete Group Invite')

@section('content')
    <h1>Delete Invite for <strong>{{ $userInvite->email }}</strong></h1>
    <p>Are you sure you want to delete this invite? The user will no longer be able to access the group.</p>
    <form action="" method="post">
        @csrf
        <a id="goBackBtn" class="btn btn-primary">Go Back</a>
        <button type="submit" class="btn btn-danger">Yes</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#goBackBtn').click(function(e) {
                e.preventDefault();
                if (history.length === 1) {
                    window.location.href = '{{ route('user-invite.index', ['group' => $group] )}}';
                } else {
                    history.back();
                }
            });
        });
    </script>
@endsection
