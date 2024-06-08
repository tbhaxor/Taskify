@extends('layouts.base')

@section('title', 'Forgot Password')

@section('content')
    <div class="row justify-content-md-center">
        <div class="col-6 card">
            <div class="card-body">
                <h3 class="card-title">Forgot Password</h3>
                <hr>
                @isset($message)
                    @if($success)
                        <div class="alert alert-success" role="alert">
                            {{ $message }}
                        </div>
                    @else
                        <div class="alert alert-danger" role="alert">
                            {{ $message }}
                        </div>
                    @endif

                @endisset

                <form action="" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required id="email" name="email"
                               placeholder="Enter your email address...">
                        @error('email')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Send Recovery Email</button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <a href="{{ url()->previous() }}" class="card-link">Go Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
