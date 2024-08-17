<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Role $role): void
    {
        UserPermission::forRole($role->name)->each(function (UserPermission $permission) use ($role) {
            $permission = Permission::createOrFirst(['value' => $permission], ['value' => $permission]);
            $role->permissions()->attach($permission->id);
        });
    }
}
