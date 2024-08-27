<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('tasks')
            ->whereNull('user_id')
            ->update([
                'user_id' => DB::raw('(SELECT user_id FROM groups WHERE groups.id = tasks.group_id)')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tasks')->update([
            'user_id' => null
        ]);
    }
};
