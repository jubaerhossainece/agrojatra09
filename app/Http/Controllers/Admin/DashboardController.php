<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Member;
use App\Models\Share;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = Member::count();
        $totalShares = Share::sum('number_of_shares');
        $totalShareAmount = Share::sum('total_amount');
        $totalDeposited = Deposit::sum('amount');
        $totalPending = $totalShareAmount - $totalDeposited;

        $recentDeposits = Deposit::with('member')
            ->latest()
            ->take(5)
            ->get();

        $pendingMembers = Member::with('shares')
            ->get()
            ->filter(fn($m) => $m->balance_due > 0)
            ->take(10);

        return view('admin.dashboard', compact(
            'totalMembers',
            'totalShares',
            'totalShareAmount',
            'totalDeposited',
            'totalPending',
            'recentDeposits',
            'pendingMembers'
        ));
    }
}
