@extends('layouts.base')

@section('title', 'Reset Password')

@section('content')
    <div class="row justify-content-md-center">
        <div class="col-6 card">
            <div class="card-body">
                <h3 class="card-title">Reset Password</h3>
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
                        <input type="email" disabled class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $email) }}" required id="email" name="email"
                               placeholder="Enter your email address...">
                        @error('email')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="token" class="form-label">Reset Token</label>
                        <input type="text" disabled class="form-control @error('token') is-invalid @enderror"
                               value="{{ old('token', $token) }}" required id="token" name="token"
                               placeholder="Enter your reset token...">
                        @error('token')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       required
                                       id="password" name="password" placeholder="Enter your new password...">
                                @error('password')
                                <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       required
                                       id="password_confirmation" name="password_confirmation"
                                       placeholder="Confirm your new password...">
                                @error('password_confirmation')
                                <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
