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
        Schema::create('monthly_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('payment_year');
            $table->tinyInteger('payment_month'); // 1–12
            $table->decimal('expected_amount', 10, 2);
            $table->date('due_date');
            $table->boolean('is_late')->default(false);
            $table->timestamps();

            $table->unique(['member_id', 'payment_year', 'payment_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_payments');
    }
};
