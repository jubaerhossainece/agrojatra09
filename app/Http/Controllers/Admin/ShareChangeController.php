<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\ShareChangeRequest;
use App\Services\MonthlyPaymentService;
use Illuminate\Http\Request;

class ShareChangeController extends Controller
{
    public function __construct(private MonthlyPaymentService $paymentService) {}

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

    public function approve(ShareChangeRequest $shareChangeRequest)
    {
        abort_if(auth()->user()->member_id !== $shareChangeRequest->member_id, 403);
        abort_if($shareChangeRequest->status !== 'pending', 403);
        abort_if($shareChangeRequest->requested_by === auth()->id(), 403, 'You cannot approve a request you initiated yourself.');

        $member = $shareChangeRequest->member;
        $this->paymentService->recordShareChange($member, $shareChangeRequest->new_shares, $shareChangeRequest);

        $shareChangeRequest->update([
            'status'      => 'approved',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Share count updated to ' . $shareChangeRequest->new_shares . '.');
    }

    public function reject(Request $request, ShareChangeRequest $shareChangeRequest)
    {
        abort_if(auth()->user()->member_id !== $shareChangeRequest->member_id, 403);
        abort_if($shareChangeRequest->status !== 'pending', 403);
        abort_if($shareChangeRequest->requested_by === auth()->id(), 403, 'You cannot reject a request you initiated yourself.');

        $shareChangeRequest->update([
            'status'      => 'rejected',
            'reviewed_at' => now(),
            'member_note' => $request->input('member_note'),
        ]);

        return back()->with('success', 'Share change request rejected.');
    }
}
