<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Member;
use App\Models\Share;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $members = Member::with(['shares', 'deposits'])->orderBy('full_name')->get();

        $statusCounts = [
            'paid'    => $members->filter(fn($m) => $m->payment_status === 'paid')->count(),
            'partial' => $members->filter(fn($m) => $m->payment_status === 'partial')->count(),
            'pending' => $members->filter(fn($m) => $m->payment_status === 'pending')->count(),
        ];

        $status = $request->query('status');
        if (in_array($status, ['paid', 'partial', 'pending'])) {
            $members = $members->filter(fn($m) => $m->payment_status === $status)->values();
        } else {
            $status = null;
        }

        // Group-wide totals (always unfiltered — these describe the whole cooperative)
        $totalShares = Share::sum('number_of_shares');
        $totalShareAmount = Share::sum('total_amount');
        $totalDeposited = Deposit::sum('amount');
        $totalPending = $totalShareAmount - $totalDeposited;

        // Table footer totals — reflect the current filter, not the whole group
        $tableTotals = [
            'shares'    => $members->sum('total_shares'),
            'amount'    => $members->sum('total_amount'),
            'deposited' => $members->sum('total_deposited'),
            'pending'   => $members->sum('balance_due'),
        ];

        return view('admin.reports.index', compact(
            'members',
            'totalShares',
            'totalShareAmount',
            'totalDeposited',
            'totalPending',
            'status',
            'statusCounts',
            'tableTotals'
        ));
    }
}
