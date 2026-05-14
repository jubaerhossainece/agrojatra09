<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use App\Models\Member;
use Illuminate\Http\Request;


class DepositController extends Controller
{
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

        $deposits = $query->paginate(15)->withQueryString();
        $members  = Member::orderBy('full_name')->get();

        return view('admin.deposits.index', compact('deposits', 'members'));
    }

    public function create(Request $request)
    {
        // Only show members who have granted permission
        $members = Member::with('shares')
            ->where('admin_deposit_permission', true)
            ->orderBy('full_name')
            ->get();

        $selectedMember = $request->member_id
            ? Member::with('shares')->find($request->member_id)
            : null;

        // If a specific member was requested but hasn't granted permission, warn admin
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
        Deposit::create($data);

        $this->syncShareStatus($member);

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Deposit recorded successfully.');
    }

    public function show(Deposit $deposit)
    {
        $deposit->load(['member', 'recorder', 'share']);
        return view('admin.deposits.show', compact('deposit'));
    }

    public function destroy(Deposit $deposit)
    {
        $deposit->delete();

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Deposit deleted.');
    }

    private function syncShareStatus(Member $member): void
    {
        $share = $member->shares()->latest()->first();
        if (!$share) return;

        $totalDeposited = $member->deposits()->sum('amount');
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
