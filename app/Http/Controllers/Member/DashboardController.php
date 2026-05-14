<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::with(['nominee', 'shares', 'deposits', 'groupOpinion'])
            ->findOrFail($user->member_id);

        return view('member.dashboard', compact('member'));
    }
}
