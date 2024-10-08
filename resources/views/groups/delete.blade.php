@extends('layouts.base')

@section('title', 'Delete Group')

@section('content')
    <h1>Delete <strong>{{ $group->title }}</strong></h1>
    <p>Are you sure you want to delete this group?</p>
    <form action="" method="post">
        @csrf
        @method('DELETE')
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
                    window.location.href = '{{ route('group.index' )}}';
                } else {
                    history.back();
                }
            });
        });
    </script>
@endsection
