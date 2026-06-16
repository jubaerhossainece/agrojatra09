<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ShareChangeRequest;
use Illuminate\Http\Request;

class ShareChangeController extends Controller
{
    public function approve(ShareChangeRequest $shareChangeRequest)
    {
        $member = auth()->user()->member;

        if ($shareChangeRequest->member_id !== $member->id || $shareChangeRequest->status !== 'pending') {
            abort(403);
        }

        // Update the first share record so the total equals the new count
        $share = $member->shares()->first();
        if ($share) {
            $share->update([
                'number_of_shares' => $shareChangeRequest->new_shares,
                'total_amount'     => $shareChangeRequest->new_shares * 2000,
            ]);
        }

        $shareChangeRequest->update([
            'status'      => 'approved',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Share change approved. Your shares have been updated to ' . $shareChangeRequest->new_shares . '.');
    }

    public function reject(Request $request, ShareChangeRequest $shareChangeRequest)
    {
        $member = auth()->user()->member;

        if ($shareChangeRequest->member_id !== $member->id || $shareChangeRequest->status !== 'pending') {
            abort(403);
        }

        $shareChangeRequest->update([
            'status'      => 'rejected',
            'reviewed_at' => now(),
            'member_note' => $request->input('member_note'),
        ]);

        return back()->with('success', 'Share change request rejected.');
    }
}
