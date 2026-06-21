<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\DepositAllocation;
use App\Models\Member;
use App\Models\MonthlyPayment;
use App\Models\ShareChangeRequest;
use App\Models\ShareHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyPaymentService
{
    const GROUP_START_YEAR  = 2026;
    const GROUP_START_MONTH = 6; // June

    /**
     * Generate MonthlyPayment records for all active members for a given year/month.
     * Skips members that already have a record for that period.
     * Returns the count of new records created.
     */
    public function generateForMonth(int $year, int $month): int
    {
        $dueDate  = $this->computeDueDate($year, $month);
        $members  = Member::with('shares')->where('status', 'active')->get();
        $created  = 0;

        foreach ($members as $member) {
            $history = ShareHistory::asOf($member->id, $year, $month);
            $expectedAmount = $history?->total_amount ?? $member->total_amount; // shares × 2000, as of this month
            if ($expectedAmount <= 0) continue;

            $exists = MonthlyPayment::where('member_id', $member->id)
                ->where('payment_year', $year)
                ->where('payment_month', $month)
                ->exists();

            if (!$exists) {
                MonthlyPayment::create([
                    'member_id'       => $member->id,
                    'payment_year'    => $year,
                    'payment_month'   => $month,
                    'expected_amount' => $expectedAmount,
                    'due_date'        => $dueDate,
                    'is_late'         => false,
                ]);
                $created++;

                // Replay all deposits so any prepaid surplus cascades into this new month.
                $this->reallocateAll($member);
            }
        }

        return $created;
    }

    /**
     * Compute due date.
     * May 2026 → 2026-05-31 (last day, so everything in May is on-time).
     * All other months → YYYY-MM-20.
     */
    public function computeDueDate(int $year, int $month): Carbon
    {
        if ($year === self::GROUP_START_YEAR && $month === self::GROUP_START_MONTH) {
            return Carbon::create($year, $month, 1)->endOfMonth()->startOfDay();
        }

        return Carbon::create($year, $month, 20)->startOfDay();
    }

    /**
     * After a deposit is approved, allocate its amount to the oldest unpaid monthly
     * payment records for that member, cascading until funds are exhausted.
     * Existing allocations for this deposit are cleared and recalculated.
     * Silently skips if the deposit is not approved.
     */
    public function allocateDeposit(Deposit $deposit): void
    {
        if ($deposit->status !== 'approved') return;

        DB::transaction(function () use ($deposit) {
            // Remove old allocations for this deposit so we can recalculate
            DepositAllocation::where('deposit_id', $deposit->id)->delete();

            $remaining = (float) $deposit->amount;

            // Get all unpaid (or partially paid) monthly payments for this member,
            // ordered oldest first
            $payments = MonthlyPayment::where('member_id', $deposit->member_id)
                ->orderBy('payment_year')
                ->orderBy('payment_month')
                ->with('allocations')
                ->get();

            foreach ($payments as $payment) {
                if ($remaining <= 0) break;

                $alreadyAllocated = (float) $payment->allocations->sum('allocated_amount');
                $stillOwed        = max(0, (float) $payment->expected_amount - $alreadyAllocated);

                if ($stillOwed <= 0) continue;

                $toAllocate = min($remaining, $stillOwed);

                DepositAllocation::create([
                    'deposit_id'         => $deposit->id,
                    'monthly_payment_id' => $payment->id,
                    'allocated_amount'   => $toAllocate,
                ]);

                $remaining -= $toAllocate;
            }

            // Mark is_late for this deposit on each monthly payment it now covers
            $this->refreshLateFlags($deposit->member_id);
        });
    }

    /**
     * Recalculate is_late for all monthly payments of a member.
     * A payment is late if deposit(s) covering it arrived after due_date.
     */
    public function refreshLateFlags(int $memberId): void
    {
        $payments = MonthlyPayment::where('member_id', $memberId)
            ->with(['allocations.deposit'])
            ->get();

        foreach ($payments as $payment) {
            $totalAllocated = $payment->allocations->sum('allocated_amount');
            if ($totalAllocated < $payment->expected_amount) {
                // Not yet fully paid — not late yet (or still pending)
                $payment->is_late = false;
            } else {
                // Find the date when it became fully paid (the deposit that tipped it over)
                $runningTotal = 0;
                $paidOnDate   = null;

                // Sort allocations by deposit date ascending
                $sorted = $payment->allocations->sortBy(fn($a) => $a->deposit->deposit_date);

                foreach ($sorted as $allocation) {
                    $runningTotal += (float) $allocation->allocated_amount;
                    if ($runningTotal >= (float) $payment->expected_amount) {
                        $paidOnDate = $allocation->deposit->deposit_date;
                        break;
                    }
                }

                $payment->is_late = $paidOnDate
                    ? $paidOnDate->gt($payment->due_date)
                    : false;
            }

            $payment->save();
        }
    }

    /**
     * Re-allocate all deposits for a member from scratch (useful after editing/deleting a deposit).
     */
    public function reallocateAll(Member $member): void
    {
        DB::transaction(function () use ($member) {
            // Wipe all allocations for this member's deposits
            DepositAllocation::whereIn(
                'deposit_id',
                $member->deposits()->pluck('id')
            )->delete();

            // Replay only approved deposits in chronological order
            $deposits = $member->deposits()->where('status', 'approved')->orderBy('deposit_date')->orderBy('id')->get();

            foreach ($deposits as $deposit) {
                $remaining = (float) $deposit->amount;

                $payments = MonthlyPayment::where('member_id', $member->id)
                    ->orderBy('payment_year')
                    ->orderBy('payment_month')
                    ->with('allocations')
                    ->get();

                foreach ($payments as $payment) {
                    if ($remaining <= 0) break;

                    $alreadyAllocated = (float) $payment->allocations
                        ->where('deposit_id', '!=', $deposit->id)
                        ->sum('allocated_amount');

                    // Fresh load to include allocations we just created in this loop
                    $alreadyAllocated = (float) DepositAllocation::where('monthly_payment_id', $payment->id)
                        ->sum('allocated_amount');

                    $stillOwed = max(0, (float) $payment->expected_amount - $alreadyAllocated);
                    if ($stillOwed <= 0) continue;

                    $toAllocate = min($remaining, $stillOwed);

                    DepositAllocation::create([
                        'deposit_id'         => $deposit->id,
                        'monthly_payment_id' => $payment->id,
                        'allocated_amount'   => $toAllocate,
                    ]);

                    $remaining -= $toAllocate;
                }
            }

            $this->refreshLateFlags($member->id);
        });
    }

    /**
     * Apply an approved share change: update the member's share count, record
     * it in share_histories effective the current year/month, then instantly
     * sync the current month's expected_amount (and any already-generated
     * future months) — past months are never touched retroactively.
     */
    public function recordShareChange(Member $member, int $newShares, ?ShareChangeRequest $shareChangeRequest = null): void
    {
        DB::transaction(function () use ($member, $newShares, $shareChangeRequest) {
            $share = $member->shares()->first();
            if ($share) {
                $share->update([
                    'number_of_shares' => $newShares,
                    'total_amount'     => $newShares * 2000,
                ]);
            }

            $now = Carbon::now();

            ShareHistory::create([
                'member_id'               => $member->id,
                'number_of_shares'        => $newShares,
                'total_amount'            => $newShares * 2000,
                'effective_year'          => $now->year,
                'effective_month'         => $now->month,
                'share_change_request_id' => $shareChangeRequest?->id,
            ]);

            $this->syncCurrentAndFuture($member);
        });
    }

    /**
     * Refresh expected_amount on a member's current-month and any already-
     * generated future-month records to match share_histories. Past months
     * are left untouched.
     */
    public function syncCurrentAndFuture(Member $member): void
    {
        $now = Carbon::now();

        $payments = MonthlyPayment::where('member_id', $member->id)
            ->where(function ($q) use ($now) {
                $q->where('payment_year', '>', $now->year)
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where('payment_year', $now->year)->where('payment_month', '>=', $now->month);
                    });
            })
            ->get();

        if ($payments->isEmpty()) {
            return;
        }

        foreach ($payments as $payment) {
            $history = ShareHistory::asOf($member->id, $payment->payment_year, $payment->payment_month);
            if ($history && (float) $history->total_amount !== (float) $payment->expected_amount) {
                $payment->update(['expected_amount' => $history->total_amount]);
            }
        }

        $this->reallocateAll($member);
    }

    /**
     * Repair tool: recompute expected_amount for every record in a given
     * month from share_histories as of that record's own month — never the
     * member's current rate. Safe to run on any month, any time. Returns the
     * number of records whose expected_amount actually changed.
     */
    public function regenerateMonth(int $year, int $month): int
    {
        $payments = MonthlyPayment::where('payment_year', $year)
            ->where('payment_month', $month)
            ->get();

        $changed = 0;
        $affectedMembers = [];

        foreach ($payments as $payment) {
            $history = ShareHistory::asOf($payment->member_id, $year, $month);
            if (!$history) continue;

            if ((float) $history->total_amount !== (float) $payment->expected_amount) {
                $payment->update(['expected_amount' => $history->total_amount]);
                $changed++;
                $affectedMembers[$payment->member_id] = true;
            }
        }

        foreach (array_keys($affectedMembers) as $memberId) {
            $this->reallocateAll(Member::find($memberId));
        }

        return $changed;
    }
}
