<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['Admin', 'Editor', 'Viewer', 'Task Manager'])->each(function ($name) {
            $role = Role::updateOrCreate(['name' => $name, 'user_id' => null], ['name' => $name]);
            $this->callSilent(PermissionSeeder::class, ['role' => $role]);
        });
    }
}
