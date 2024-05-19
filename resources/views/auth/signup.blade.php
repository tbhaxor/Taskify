@extends('layouts.base')

@section('title', 'Signup')

@section('content')
<div class="row justify-content-md-center">
<div class="col-6 card">
    <div class="card-body">
        <h3 class="card-title">Signup</h3>
        <hr>
        @isset($error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endisset
        <form action="" method="post">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required id="name" name="name" placeholder="Enter your full name...">
                @error('name')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required id="email" name="email" placeholder="Enter your email address...">
                @error('email')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" required id="password" name="password" placeholder="Enter your password...">
                    @error('password')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" required id="confirm_password" name="confirm_password" placeholder="Confirm your password...">
                    @error('confirm_password')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
               
            </div>
            <div class="d-grid mb-3">
                <button class="btn btn-primary" type="submit">Signup</button>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    Already a user? 
                    <a href="{{ route('auth.login') }}" class="card-link">Login</a> now!
                </div>
            </div>
        </form>
    </div>
  </div>
</div>

@endsection