<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('position', ['president', 'secretary', 'accountant'])
                  ->nullable()
                  ->after('role');
        });

        // Existing admin account(s) become president
        DB::table('users')->where('role', 'admin')->update(['position' => 'president']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
