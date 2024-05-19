<?php

use App\Http\Controllers\Groups\CreateGroupController;
use App\Http\Controllers\Groups\DeleteGroupController;
use App\Http\Controllers\Groups\EditGroupController;
use App\Http\Controllers\Groups\ListGroupController;
use App\Http\Controllers\Groups\ShowGroupController;
use App\Http\Controllers\ProfileSettings\DeleteProfileSettingsController;
use App\Http\Controllers\ProfileSettings\UpdateProfileSettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\GroupTaskValidationMiddleware;
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
        ->controller(TaskController::class)
        ->prefix('groups/{group}/tasks')
        ->middleware(GroupTaskValidationMiddleware::class)
        ->group(function () {
            Route::match(['GET', 'POST'], 'create', 'create')->name('create');
            Route::missing(function () {
                return to_route('group.show', [
                    'error' => 'Requested task does not exist.',
                    'group' => request()->route()->parameters['group']
                ]);
            })->group(function () {
                Route::get('{task}', 'show')->name('show');
                Route::match(['GET', 'PUT'], '{task}/edit', 'edit')->name('edit');
                Route::match(['GET', 'DELETE'], '{task}/delete', 'delete')->name('delete');
            });
        });
});
