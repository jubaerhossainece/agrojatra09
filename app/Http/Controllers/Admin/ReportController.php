<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Member;
use App\Models\Share;

class ReportController extends Controller
{
    public function index()
    {
        $members = Member::with(['shares', 'deposits'])->orderBy('full_name')->get();

        $totalShares = Share::sum('number_of_shares');
        $totalShareAmount = Share::sum('total_amount');
        $totalDeposited = Deposit::sum('amount');
        $totalPending = $totalShareAmount - $totalDeposited;

        return view('admin.reports.index', compact(
            'members',
            'totalShares',
            'totalShareAmount',
            'totalDeposited',
            'totalPending'
        ));
    }
}
