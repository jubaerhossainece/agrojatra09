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

        $pendingShareChange  = $member->shareChangeRequests()->where('status', 'pending')->latest()->first();
        $shareChangeHistory  = $member->shareChangeRequests()->with('requestedBy')->latest()->get();

        return view('member.dashboard', compact('member', 'pendingShareChange', 'shareChangeHistory'));
    }
}
