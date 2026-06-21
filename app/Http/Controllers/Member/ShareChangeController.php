<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ShareChangeRequest;
use App\Services\MonthlyPaymentService;
use Illuminate\Http\Request;

class ShareChangeController extends Controller
{
    public function __construct(private MonthlyPaymentService $paymentService) {}

    public function approve(ShareChangeRequest $shareChangeRequest)
    {
        $member = auth()->user()->member;

        if ($shareChangeRequest->member_id !== $member->id || $shareChangeRequest->status !== 'pending') {
            abort(403);
        }

        $this->paymentService->recordShareChange($member, $shareChangeRequest->new_shares, $shareChangeRequest);

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
