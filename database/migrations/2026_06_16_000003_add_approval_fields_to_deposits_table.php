<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('recorded_by');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        // All existing deposits were already counted — mark them approved
        DB::table('deposits')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['status', 'approved_at']);
        });
    }
};
