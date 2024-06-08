<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignupController;
use Illuminate\Support\Facades\Route;


Route::name('auth.')->prefix('auth')->group(function () {
    Route::get('logout', LogoutController::class)->name('logout')->middleware('auth');

    Route::middleware('guest')->group(function () {
        Route::match(['GET', 'POST'], 'login', LoginController::class)->name('login');
        Route::match(['GET', 'POST'], 'signup', SignupController::class)->name('signup');

        Route::prefix('password')->name('password.')->group(function () {
            Route::match(['GET', 'POST'], 'forgot', ForgotPasswordController::class)->name('forgot');
            Route::match(['GET', 'POST'], 'reset', ResetPasswordController::class)->name('reset');
        });
    });

});

