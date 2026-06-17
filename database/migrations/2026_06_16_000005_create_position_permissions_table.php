<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->string('permission');
            $table->timestamps();
            $table->unique(['position', 'permission']);
        });

        // Initial seed: accountant is responsible for deposit approvals
        DB::table('position_permissions')->insert([
            ['position' => 'accountant', 'permission' => 'approve_deposits', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('position_permissions');
    }
};
