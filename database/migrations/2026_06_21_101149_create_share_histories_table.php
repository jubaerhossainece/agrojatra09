<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('share_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number_of_shares');
            $table->decimal('total_amount', 12, 2);
            $table->unsignedSmallInteger('effective_year');
            $table->unsignedTinyInteger('effective_month');
            $table->foreignId('share_change_request_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['member_id', 'effective_year', 'effective_month'], 'share_histories_member_period_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_histories');
    }
};
