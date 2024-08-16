<?php

use App\Http\Controllers\Auth\CallbackController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;


Route::name('auth.')->prefix('auth')->group(function () {
    Route::get('login', LoginController::class)->name('login');
    Route::get('logout', LogoutController::class)->name('logout');
    Route::get('callback', CallbackController::class)->name('callback');
});
