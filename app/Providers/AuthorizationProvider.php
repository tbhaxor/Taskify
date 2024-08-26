<?php

namespace App\Providers;

use App\Enums\UserPermission;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthorizationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        UserPermission::all()->each(function (UserPermission $permission) {
            Gate::define($permission->value, function (User $user, Group $group) use ($permission) {
                return $user->roleOnGroup($group)
                    ->first()
                    ?->permissions
                    ->map
                    ->value
                    ->contains($permission);
            });
        });
    }
}
