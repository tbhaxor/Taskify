<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\Artisan::call('db:seed');

        DB::table('groups')->select(['id', 'user_id'])->get()->each(function ($row) {
            DB::table('user_group_roles')->insertOrIgnore([
                'user_id' => $row->user_id,
                'group_id' => $row->id,
                'role_id' => Role::admin()->id
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
