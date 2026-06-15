<?php

namespace App\Console\Commands;

use App\Services\MonthlyPaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyPayments extends Command
{
    protected $signature = 'payments:generate
                            {--year= : Year (defaults to current)}
                            {--month= : Month 1-12 (defaults to current)}';

    protected $description = 'Generate monthly payment records for all active members';

    public function handle(MonthlyPaymentService $service): int
    {
        $year  = (int) ($this->option('year')  ?: Carbon::now()->year);
        $month = (int) ($this->option('month') ?: Carbon::now()->month);

        if ($month < 1 || $month > 12) {
            $this->error('Month must be between 1 and 12.');
            return 1;
        }

        // Don't generate before group start
        $target = Carbon::create($year, $month, 1);
        $start  = Carbon::create(MonthlyPaymentService::GROUP_START_YEAR, MonthlyPaymentService::GROUP_START_MONTH, 1);

        if ($target->lt($start)) {
            $this->error("Cannot generate payments before group start ({$start->format('M Y')}).");
            return 1;
        }

        $this->info("Generating payments for {$target->format('F Y')}...");
        $created = $service->generateForMonth($year, $month);
        $this->info("Done — {$created} new record(s) created.");

        return 0;
    }
}
