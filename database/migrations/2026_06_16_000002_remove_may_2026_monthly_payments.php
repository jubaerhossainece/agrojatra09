<?php

use App\Models\Member;
use App\Models\MonthlyPayment;
use App\Services\MonthlyPaymentService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // reallocateAll() below filters deposits by `status`, which the
        // 2026_06_16_000003 migration normally adds — but that one is numbered
        // to run after this one. Add it here first if missing, so this works
        // on a fresh database; 000003 is itself guarded to skip re-adding it.
        if (! Schema::hasColumn('deposits', 'status')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('recorded_by');
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            });

            DB::table('deposits')->update(['status' => 'approved']);
        }

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
