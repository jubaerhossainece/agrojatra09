<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MonthlyPayment;

class PaymentScheduleController extends Controller
{
    public function index()
    {
        $member = Member::with('shares')->findOrFail(auth()->user()->member_id);

        $payments = MonthlyPayment::where('member_id', $member->id)
            ->orderBy('payment_year')
            ->orderBy('payment_month')
            ->with('allocations.deposit')
            ->get();

        return view('member.payment-schedule', compact('member', 'payments'));
    }
}
