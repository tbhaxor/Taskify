<?php

use App\Http\Controllers\Groups\CreateGroupController;
use App\Http\Controllers\Groups\DeleteGroupController;
use App\Http\Controllers\Groups\EditGroupController;
use App\Http\Controllers\Groups\ListGroupController;
use App\Http\Controllers\Groups\ShowGroupController;
use App\Http\Controllers\ProfileSettings\DeleteProfileSettingsController;
use App\Http\Controllers\ProfileSettings\UpdateProfileSettingsController;
use App\Http\Controllers\Tasks\CreateTaskController;
use App\Http\Controllers\Tasks\DeleteTaskController;
use App\Http\Controllers\Tasks\EditTaskController;
use App\Http\Controllers\Tasks\ShowTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


require 'auth.php';

Route::middleware('auth')->group(function () {
    Route::name('profile.')->prefix('profile')->group(function () {
        Route::match(['GET', 'POST'], 'edit', UpdateProfileSettingsController::class)->name('edit');
        Route::post('delete', DeleteProfileSettingsController::class)->name('delete');
    });

    Route::name('group.')->prefix('groups')->group(function () {
        Route::get('', ListGroupController::class)->name('index');
        Route::match(['GET', 'POST'], 'create', CreateGroupController::class)->name('create');
        Route::missing(function () {
            return to_route('group.index', [
                'error' => 'Requested resource does not exist.',
            ]);
        })->group(function () {
            Route::get('{group}', ShowGroupController::class)->name('show');
            Route::match(['GET', 'PUT'], '{group}/edit', EditGroupController::class)->name('edit');
            Route::match(['GET', 'DELETE'], '{group}/delete', DeleteGroupController::class)->name('delete');
        });
    });

    Route::name('task.')
        ->scopeBindings() // checks if the child model is really a child of the parent
        ->prefix('groups/{group}/tasks')
        ->group(function () {
            Route::match(['GET', 'POST'], 'create', CreateTaskController::class)->name('create');
            Route::missing(function () {
                return to_route('group.show', [
                    'error' => 'Requested task does not exist.',
                    'group' => request()->route()->parameters['group']
                ]);
            })->group(function () {
                Route::get('{task}', ShowTaskController::class)->name('show');
                Route::match(['GET', 'PUT'], '{task}/edit', EditTaskController::class)->name('edit');
                Route::match(['GET', 'DELETE'], '{task}/delete', DeleteTaskController::class)->name('delete');
            });
        });
});
