<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SignupController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::match(['GET', 'POST'], '/auth/login', LoginController::class)->name('auth.login');
    Route::match(['GET', 'POST'], '/auth/signup', SignupController::class)->name('auth.signup');
});

Route::middleware('auth')->group(function () {
    Route::get('/auth/logout', LogoutController::class)->name('auth.logout');
});
