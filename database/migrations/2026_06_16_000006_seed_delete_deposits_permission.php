<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('position_permissions')->insertOrIgnore([
            ['position' => 'accountant', 'permission' => 'delete_deposits', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Cache::forget('position_perms_accountant');
    }

    public function down(): void
    {
        DB::table('position_permissions')
            ->where('position', 'accountant')
            ->where('permission', 'delete_deposits')
            ->delete();

        Cache::forget('position_perms_accountant');
    }
};
