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
        Schema::create('deposit_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monthly_payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('allocated_amount', 10, 2);
            $table->timestamps();

            $table->unique(['deposit_id', 'monthly_payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_allocations');
    }
};
