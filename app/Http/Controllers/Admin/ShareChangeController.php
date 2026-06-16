<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\ShareChangeRequest;
use Illuminate\Http\Request;

class ShareChangeController extends Controller
{
    public function store(Request $request, Member $member)
    {
        $request->validate([
            'new_shares' => 'required|integer|min:1|max:100',
            'admin_note' => 'nullable|string|max:255',
        ]);

        // Cancel any currently pending request for this member
        $member->shareChangeRequests()->where('status', 'pending')->update(['status' => 'cancelled']);

        ShareChangeRequest::create([
            'member_id'    => $member->id,
            'requested_by' => auth()->id(),
            'old_shares'   => $member->total_shares,
            'new_shares'   => (int) $request->new_shares,
            'status'       => 'pending',
            'admin_note'   => $request->admin_note,
        ]);

        return back()->with('success', 'Share change request sent to ' . $member->full_name . ' for approval.');
    }
}
