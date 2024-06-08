@extends('layouts.base')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-md-center">
        <div class="col-6 card">
            <div class="card-body">
                <h3 class="card-title">Login</h3>
                <hr>
                @error('credentials')
                <div class="alert alert-danger" role="alert">
                    {{ $message }}
                </div>
                @enderror
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
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               required
                               id="password" name="password" placeholder="Enter your password...">
                        @error('password')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="checkbox" name="remember" id="remember" value="1"/>
                            <label for="remember" class="form-label">Remember Login</label>
                        </div>
                        <div class="col">
                            <a href="{{ route('auth.password.forgot') }}" class="float-end card-link">Forgot
                                Password?</a>
                        </div>
                    </div>
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Login</button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            Not a user?
                            <a href="{{ route('auth.signup') }}" class="card-link">Signup</a> now!

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const qp = new URLSearchParams(window.location.search)
            if (qp.has('message')) {
                $('hr').after(`<div class="alert alert-success" role="alert">${qp.get('message')}</div>`)
            }
        })
    </script>
@endsection
