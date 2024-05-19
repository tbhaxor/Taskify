@extends('layouts.base')

@section('title', 'Unauthorized Resource')

@section('content')
<div class="bg-light">
    <div class="d-flex justify-content-center align-items-center">
        <div class="card text-center shadow">
            <div class="card-body">
                <h1 class="card-title">403 Unauthorized</h1>
                <svg class="error-illustration mb-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path fill="#ff6b6b" d="M12 2a10 10 0 0 0-10 10 10 10 0 0 0 20 0 10 10 0 0 0-10-10zm-1 15h2v2h-2zm2-10h-2v8h2z"/>
                </svg>
                <p class="card-text">You do not have permission to view this page.</p>
                <button class="btn btn-primary" onclick="history.back()">Go Back</button>
            </div>
        </div>
    </div>
</div>
@endsection