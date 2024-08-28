@extends('layouts.base')

@section('title', 'Delete Group Sharing')

@section('content')
    <h1>Delete Sharing Access</h1>
    <p>Are you sure you want to delete the sharing access of <strong>{{ $userGroupRole->user->email }}</strong>
        for {{ $group->title }}?</p>
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
                    window.location.href = '{{ route('group-sharing.index', ['group' => $group] )}}';
                } else {
                    history.back();
                }
            });
        });
    </script>
@endsection
