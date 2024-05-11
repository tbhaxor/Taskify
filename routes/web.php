<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::name('task.')->controller(TaskController::class)->prefix('tasks')->group(function () {
    Route::get('', 'index')->name('index');
    Route::match(['GET', 'POST'], 'create', 'create')->name('create');
    Route::missing(function () {
        return to_route('task.index', [
            'error' => 'Requested resource does not exist.',
        ]);
    })->group(function () {
        Route::get('{task}', 'show')->name('show');
        Route::match(['GET', 'PUT'], '{task}/edit', 'edit')->name('edit');
        Route::match(['GET', 'DELETE'], '{task}/delete', 'delete')->name('delete');
    });
});
