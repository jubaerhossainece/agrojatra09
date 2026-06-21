<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MonthlyPayment;
use App\Services\MonthlyPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonthlyPaymentController extends Controller
{
    public function __construct(private MonthlyPaymentService $service) {}

    /**
     * Matrix view: members × months grid.
     */
    public function index(Request $request)
    {
        $year = (int) ($request->query('year', Carbon::now()->year));

        // Collect all months that have records (or default to current group range)
        $startMonth = ($year === MonthlyPaymentService::GROUP_START_YEAR)
            ? MonthlyPaymentService::GROUP_START_MONTH
            : 1;

        $months = range($startMonth, 12);

        $members = Member::with(['shares', 'monthlyPayments' => function ($q) use ($year) {
            $q->where('payment_year', $year)->with('allocations');
        }])->where('status', 'active')->orderBy('full_name')->get();

        return view('admin.monthly-payments.index', compact('members', 'months', 'year'));
    }

    /**
     * Detail view for one month (all members' status for that month).
     */
    public function show(Request $request, int $year, int $month)
    {
        $payments = MonthlyPayment::with(['member', 'allocations.deposit'])
            ->where('payment_year', $year)
            ->where('payment_month', $month)
            ->orderBy('member_id')
            ->get()
            ->sortBy(fn($p) => $p->member->full_name);

        $statusCounts = [
            'paid'    => $payments->filter(fn($p) => $p->is_paid)->count(),
            'partial' => $payments->filter(fn($p) => !$p->is_paid && $p->total_allocated > 0)->count(),
            'unpaid'  => $payments->filter(fn($p) => $p->total_allocated <= 0)->count(),
        ];

        $status = $request->query('status');
        if (in_array($status, ['paid', 'partial', 'unpaid'])) {
            $payments = $payments->filter(function ($p) use ($status) {
                return match ($status) {
                    'paid'    => $p->is_paid,
                    'partial' => !$p->is_paid && $p->total_allocated > 0,
                    'unpaid'  => $p->total_allocated <= 0,
                };
            });
        } else {
            $status = null;
        }

        $label = Carbon::create($year, $month, 1)->format('F Y');

        return view('admin.monthly-payments.show', compact('payments', 'label', 'year', 'month', 'status', 'statusCounts'));
    }

    /**
     * Generate records for a given month (admin-triggered).
     */
    public function generate(Request $request)
    {
        $request->validate([
            'year'  => 'required|integer|min:2026',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year  = (int) $request->year;
        $month = (int) $request->month;

        $target = Carbon::create($year, $month, 1);
        $start  = Carbon::create(MonthlyPaymentService::GROUP_START_YEAR, MonthlyPaymentService::GROUP_START_MONTH, 1);

        if ($target->lt($start)) {
            return back()->with('error', 'Cannot generate payments before group start (' . $start->format('M Y') . ').');
        }

        $created = $this->service->generateForMonth($year, $month);

        $label = $target->format('F Y');
        $msg   = $created > 0
            ? "Generated {$created} payment record(s) for {$label}."
            : "All records for {$label} already exist — nothing to generate.";

        return back()->with('success', $msg);
    }

    /**
     * Bulk-update the due date for all records of a given month.
     */
    public function updateDueDate(Request $request, int $year, int $month)
    {
        $request->validate(['due_date' => 'required|date']);

        $updated = MonthlyPayment::where('payment_year', $year)
            ->where('payment_month', $month)
            ->update(['due_date' => $request->due_date]);

        // Re-check late flags since the due date changed
        $memberIds = MonthlyPayment::where('payment_year', $year)
            ->where('payment_month', $month)
            ->pluck('member_id');

        foreach ($memberIds as $id) {
            $this->service->refreshLateFlags($id);
        }

        $label = Carbon::create($year, $month, 1)->format('F Y');

        return back()->with('success', "Due date updated for {$label} ({$updated} records).");
    }

    /**
     * Repair tool: re-sync expected_amount on this month's records to the
     * member's historically-correct share rate for that month, then re-run
     * allocation. Does not touch any other month.
     */
    public function regenerate(int $year, int $month)
    {
        abort_if(!auth()->user()->canRegenerateMonthlyPayments(), 403, 'You do not have permission to regenerate monthly payments.');

        $changed = $this->service->regenerateMonth($year, $month);

        $label = Carbon::create($year, $month, 1)->format('F Y');
        $msg   = $changed > 0
            ? "Regenerated {$label}: {$changed} record(s) updated to reflect current share history."
            : "Regenerated {$label}: all records already up to date — nothing to change.";

        return back()->with('success', $msg);
    }

    /**
     * Delete all payment records for a month and reallocate deposits.
     */
    public function deleteMonth(int $year, int $month)
    {
        $start = Carbon::create(MonthlyPaymentService::GROUP_START_YEAR, MonthlyPaymentService::GROUP_START_MONTH, 1);
        $target = Carbon::create($year, $month, 1);

        if ($target->lt($start)) {
            return back()->with('error', 'Cannot delete payments before group start.');
        }

        $memberIds = MonthlyPayment::where('payment_year', $year)
            ->where('payment_month', $month)
            ->pluck('member_id');

        MonthlyPayment::where('payment_year', $year)
            ->where('payment_month', $month)
            ->delete(); // deposit_allocations cascade via FK

        $members = \App\Models\Member::whereIn('id', $memberIds)->get();
        foreach ($members as $member) {
            $this->service->reallocateAll($member);
        }

        $label = $target->format('F Y');

        return redirect()->route('admin.monthly-payments.index', ['year' => $year])
            ->with('success', "Deleted all payment records for {$label}. Deposits have been reallocated.");
    }

    /**
     * Override is_late to false for a single payment record.
     */
    public function overrideLate(MonthlyPayment $monthlyPayment)
    {
        $monthlyPayment->update(['is_late' => false]);

        return back()->with('success', "Marked {$monthlyPayment->member->full_name}'s {$monthlyPayment->month_label} payment as on-time.");
    }
}
