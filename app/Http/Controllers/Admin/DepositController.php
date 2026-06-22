<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use App\Models\Member;
use App\Services\MonthlyPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DepositController extends Controller
{
    public function __construct(private MonthlyPaymentService $paymentService) {}

    public function index(Request $request)
    {
        $query = Deposit::with(['member', 'recorder'])->latest();

        if ($request->member_id) {
            $query->where('member_id', $request->member_id);
        }
        if ($request->date_from) {
            $query->whereDate('deposit_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('deposit_date', '<=', $request->date_to);
        }

        $status = $request->status; // pending | approved | rejected | null (all)
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $deposits      = $query->paginate(15)->withQueryString();
        $members       = Member::orderBy('full_name')->get();
        $pendingCount  = Deposit::where('status', 'pending')->count();

        return view('admin.deposits.index', compact('deposits', 'members', 'pendingCount', 'status'));
    }

    public function create(Request $request)
    {
        $members = Member::with('shares')
            ->where('admin_deposit_permission', true)
            ->orderBy('full_name')
            ->get();

        $selectedMember = $request->member_id
            ? Member::with('shares')->find($request->member_id)
            : null;

        if ($selectedMember && !$selectedMember->allowsAdminDeposit()) {
            return redirect()->route('admin.deposits.create')
                ->with('error', "{$selectedMember->full_name} has not granted permission for admin deposits.");
        }

        return view('admin.deposits.create', compact('members', 'selectedMember'));
    }

    public function store(StoreDepositRequest $request)
    {
        $data   = $request->validated();
        $member = Member::findOrFail($data['member_id']);

        if (!$member->allowsAdminDeposit()) {
            return back()->with('error', "{$member->full_name} has not granted permission for admin deposits.");
        }

        $data['recorded_by'] = auth()->id();
        $data['status']      = 'pending';
        $data['approved_by'] = auth()->id();
        $data['approved_at'] = now();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')
                ->store("deposits/{$member->id}", 'public');
        }

        $deposit = Deposit::create($data);

        $this->paymentService->allocateDeposit($deposit);
        $this->syncShareStatus($member);

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Deposit recorded and approved.');
    }

    public function show(Deposit $deposit)
    {
        $deposit->load(['member', 'recorder', 'share', 'approver']);
        return view('admin.deposits.show', compact('deposit'));
    }

    public function approve(Deposit $deposit)
    {
        abort_if(!auth()->user()->canApproveDeposits(), 403, 'Only the accountant or president can approve deposits.');

        if (!$deposit->isPending()) {
            return back()->with('error', 'Only pending deposits can be approved.');
        }

        $deposit->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->paymentService->allocateDeposit($deposit);
        $this->syncShareStatus($deposit->member);

        return back()->with('success', "{$deposit->member->full_name}'s deposit of ৳" . number_format((float) $deposit->amount) . " has been approved.");
    }

    public function reject(Deposit $deposit)
    {
        abort_if(!auth()->user()->canApproveDeposits(), 403, 'Only the accountant or president can reject deposits.');

        if (!$deposit->isPending()) {
            return back()->with('error', 'Only pending deposits can be rejected.');
        }

        $deposit->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "{$deposit->member->full_name}'s deposit has been rejected.");
    }

    public function destroy(Deposit $deposit)
    {
        abort_if(!auth()->user()->canDeleteDeposits(), 403, 'Only the accountant can delete deposits.');

        $member      = $deposit->member;
        $wasApproved = $deposit->isApproved();

        if ($deposit->attachment) {
            Storage::disk('public')->delete($deposit->attachment);
        }

        $deposit->delete();

        if ($wasApproved) {
            $this->paymentService->reallocateAll($member);
            $this->syncShareStatus($member);
        }

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Deposit deleted.');
    }

    private function syncShareStatus(Member $member): void
    {
        $share = $member->shares()->latest()->first();
        if (!$share) return;

        $totalDeposited = $member->deposits()->where('status', 'approved')->sum('amount');
        $totalAmount    = $member->total_amount;

        if ($totalDeposited >= $totalAmount) {
            $share->update(['status' => 'paid']);
        } elseif ($totalDeposited > 0) {
            $share->update(['status' => 'partial']);
        } else {
            $share->update(['status' => 'pending']);
        }
    }
}
