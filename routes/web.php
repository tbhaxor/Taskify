<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');



Route::get('/hello', function () {
    dd(env('SHELL'));
});
Route::name('group.')->controller(GroupController::class)->prefix('groups')->group(function () {
    Route::get('', 'index')->name('index');
    Route::match(['GET', 'POST'], 'create', 'create')->name('create');
    Route::missing(function () {
        return to_route('group.index', [
            'error' => 'Requested resource does not exist.',
        ]);
    })->group(function () {
        Route::get('{group}', 'show')->name('show');
        Route::match(['GET', 'PUT'], '{group}/edit', 'edit')->name('edit');
        Route::match(['GET', 'DELETE'], '{group}/delete', 'delete')->name('delete');
    });
});

Route::name('task.')
    ->controller(TaskController::class)
    ->prefix('groups/{group}/tasks')
    ->group(function () {
        Route::match(['GET', 'POST'], 'create', 'create')->name('create');
        Route::missing(fn () => to_route('task.index', ['error' => 'Requested resource does not exist.']))->group(function () {
            Route::get('{task}', 'show')->name('show');
            Route::match(['GET', 'PUT'], '{task}/edit', 'edit')->name('edit');
            Route::match(['GET', 'DELETE'], '{task}/delete', 'delete')->name('delete');
        });
    });
