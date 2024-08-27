<?php

use App\Http\Controllers\Groups\CreateGroupController;
use App\Http\Controllers\Groups\DeleteGroupController;
use App\Http\Controllers\Groups\EditGroupController;
use App\Http\Controllers\Groups\ListGroupController;
use App\Http\Controllers\Groups\ShowGroupController;
use App\Http\Controllers\Role\CreateRoleController;
use App\Http\Controllers\Role\DeleteRoleController;
use App\Http\Controllers\Role\EditRoleController;
use App\Http\Controllers\Role\ListRoleController;
use App\Http\Controllers\Role\ShowRoleController;
use App\Http\Controllers\Tasks\CreateTaskController;
use App\Http\Controllers\Tasks\DeleteTaskController;
use App\Http\Controllers\Tasks\EditTaskController;
use App\Http\Controllers\Tasks\ShowTaskController;
use App\Http\Controllers\UserInvite\CreateUserInviteController;
use App\Http\Controllers\UserInvite\DeleteUserInviteController;
use App\Http\Controllers\UserInvite\EditUserInviteController;
use App\Http\Controllers\UserInvite\ListUserInviteController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

require 'auth.php';

Route::middleware('auth')->group(function () {
    Route::name('group.')
        ->prefix('groups')
        ->group(function () {
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

    Route::name('user-invite.')
        ->prefix('groups/{group}/user-invites')
        ->group(function () {
            Route::get('', ListUserInviteController::class)->name('index');
            Route::match(['GET', 'POST'], 'create', CreateUserInviteController::class)->name('create');
            Route::missing(function () {
                return to_route('user-invite.index', [
                    'error' => 'Requested user invite does not exist.',
                    'group' => request()->route('group')
                ]);
            })->group(function () {
                Route::match(['GET', 'POST'], '{userInvite}/edit', EditUserInviteController::class)->name('edit');
                Route::match(['GET', 'POST'], '{userInvite}/delete', DeleteUserInviteController::class)->name('delete');
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
                    'group' => request()->route('group')
                ]);
            })->group(function () {
                Route::get('{task}', ShowTaskController::class)->name('show');
                Route::match(['GET', 'PUT'], '{task}/edit', EditTaskController::class)->name('edit');
                Route::match(['GET', 'DELETE'], '{task}/delete', DeleteTaskController::class)->name('delete');
            });
        });

    Route::name('role.')->prefix('roles')->group(function () {
        Route::get('', ListRoleController::class)->name('index');
        Route::match(['GET', 'POST'], 'create', CreateRoleController::class)->name('create');

        Route::prefix('{role}')->missing(function () {
            return to_route('role.index', [
                'error' => 'Requested resource does not exist.',
            ]);
        })->group(function () {
            Route::get('', ShowRoleController::class)->name('show');
            Route::match(['GET', 'POST'], 'edit', EditRoleController::class)->name('edit');
            Route::match(['GET', 'POST'], 'delete', DeleteRoleController::class)->name('delete');
        });
    });
});
