@extends('layouts.base')

@section('title', 'Edit Profile')

@section('content')
    <h1>Edit User Profile</h1>
    <div class="card mb-5">
        <div class="card-body">
            <form action="{{ route('profile.edit') }}" method="post">
                @csrf
                <div class="card-title"><h5>Generic Details</h5></div>
                <h6 class="card-subtitle mb-2 text-muted">All fields in this section are required</h6>
                <div class="row mb-3">
                    <div class="col">
                        <label for="name" class="form-label">Full Name <strong class="text-danger">*</strong></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name', auth()->user()->name) }}" id="name" name="name" placeholder="Enter your full name....">
                        @error('name')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <strong class="text-danger">*</strong></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email', auth()->user()->email) }}" id="email" name="email" placeholder="Enter email address....">
                            @error('email')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card-title"><h5>Update Password</h5></div>
                <h6 class="card-subtitle mb-2 text-muted">If old password is not empty, then new password and confirm new password are required.</h6>
                <div class="row mb-3">
                    <div class="col">
                        <label for="old_password" class="form-label">Old Password</label>
                        <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password" name="old_password" placeholder="Enter the old password....">
                        @error('old_password')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="col">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" placeholder="Enter the new password....">
                            @error('new_password')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col">
                        <div class="col">
                            <label for="confirm_new_password" class="form-label">Re-enter New Password</label>
                            <input type="password" class="form-control @error('confirm_new_password') is-invalid @enderror" id="confirm_new_password" name="confirm_new_password" placeholder="Re-enter the new password to confirm....">
                            @error('confirm_new_password')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Delete Account</h5>
            <p class="card-text">Are you sure you want to delete your account? This action cannot be undone.</p>
            <form action="{{ route('profile.delete') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Delete Account</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('input[name=old_password]').on('propertychange input', function () {
                if ($(this).val().length > 0) {
                    $(this).prop('required', true);
                    $('input[name=new_password]').prop('required', true);
                    $('input[name=confirm_new_password]').prop('required', true);

                    // Add asterisk to labels if not already present
                    if (!$('label[for=old_password] strong.text-danger').length) {
                        $('label[for=old_password]').append(' <strong class="text-danger">*</strong>');
                    }
                    if (!$('label[for=new_password] strong.text-danger').length) {
                        $('label[for=new_password]').append(' <strong class="text-danger">*</strong>');
                    }
                    if (!$('label[for=confirm_new_password] strong.text-danger').length) {
                        $('label[for=confirm_new_password]').append(' <strong class="text-danger">*</strong>');
                    }
                } else {
                    $(this).removeAttr('required');
                    $('input[name=new_password]').removeAttr('required');
                    $('input[name=confirm_new_password]').removeAttr('required');
                    
                    // Remove asterisk from labels
                    $('label[for=old_password] strong.text-danger').remove();
                    $('label[for=new_password] strong.text-danger').remove();
                    $('label[for=confirm_new_password] strong.text-danger').remove();
                }
            })
        })
    </script>
@endsection