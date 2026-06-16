<?php

use App\Models\Member;
use App\Models\MonthlyPayment;
use App\Services\MonthlyPaymentService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Delete all May 2026 payment records; deposit_allocations cascade-delete via FK
        MonthlyPayment::where('payment_year', 2026)
            ->where('payment_month', 5)
            ->delete();

        // Reallocate all members so any deposits that pointed to May now flow to June+
        $service = app(MonthlyPaymentService::class);
        Member::where('status', 'active')->each(fn ($m) => $service->reallocateAll($m));
    }

    public function down(): void
    {
        // Regenerate May 2026 records (no deposit re-allocation on rollback)
        $service = app(MonthlyPaymentService::class);
        $service->generateForMonth(2026, 5);
    }
};
