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

        $label = Carbon::create($year, $month, 1)->format('F Y');

        return view('admin.monthly-payments.show', compact('payments', 'label', 'year', 'month'));
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
}
