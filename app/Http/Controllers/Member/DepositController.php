<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Member;
use App\Services\MonthlyPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DepositController extends Controller
{
    public function __construct(private MonthlyPaymentService $paymentService) {}

    private function member(): Member
    {
        return Member::with(['shares', 'deposits'])->findOrFail(auth()->user()->member_id);
    }

    public function index()
    {
        $member   = $this->member();
        $deposits = $member->deposits()->latest()->get();

        return view('member.deposits.index', compact('member', 'deposits'));
    }

    public function create()
    {
        $member = $this->member();
        return view('member.deposits.create', compact('member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'         => ['required', 'numeric', 'min:1'],
            'deposit_date'   => ['required', 'date'],
            'bank_name'      => ['nullable', 'string', 'max:255'],
            'bank_reference' => ['nullable', 'string', 'max:255'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
            'note'           => ['nullable', 'string'],
            'attachment'     => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
        ]);

        $member  = $this->member();
        $share   = $member->shares()->latest()->first();

        $attachment = $request->hasFile('attachment')
            ? $request->file('attachment')->store("deposits/{$member->id}", 'public')
            : null;

        Deposit::create([
            'member_id'      => $member->id,
            'share_id'       => $share?->id,
            'amount'         => $request->amount,
            'deposit_date'   => $request->deposit_date,
            'bank_name'      => $request->bank_name,
            'bank_reference' => $request->bank_reference,
            'receipt_number' => $request->receipt_number,
            'note'           => $request->note,
            'attachment'     => $attachment,
            'recorded_by'    => auth()->id(),
            'status'         => 'pending',
        ]);

        // No allocation until admin approves
        return redirect()->route('member.deposits.index')
            ->with('success', 'Deposit submitted and is pending admin approval.');
    }

    public function edit(Deposit $deposit)
    {
        $this->authorizeDeposit($deposit);
        abort_if(!$deposit->isPending(), 403, 'Only pending deposits can be edited.');
        $member = $this->member();

        return view('member.deposits.edit', compact('deposit', 'member'));
    }

    public function update(Request $request, Deposit $deposit)
    {
        $this->authorizeDeposit($deposit);
        abort_if(!$deposit->isPending(), 403, 'Only pending deposits can be edited.');

        $request->validate([
            'amount'            => ['required', 'numeric', 'min:1'],
            'deposit_date'      => ['required', 'date'],
            'bank_name'         => ['nullable', 'string', 'max:255'],
            'bank_reference'    => ['nullable', 'string', 'max:255'],
            'receipt_number'    => ['nullable', 'string', 'max:255'],
            'note'              => ['nullable', 'string'],
            'attachment'        => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
            'remove_attachment' => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'amount'         => $request->amount,
            'deposit_date'   => $request->deposit_date,
            'bank_name'      => $request->bank_name,
            'bank_reference' => $request->bank_reference,
            'receipt_number' => $request->receipt_number,
            'note'           => $request->note,
        ];

        if ($request->hasFile('attachment')) {
            if ($deposit->attachment) {
                Storage::disk('public')->delete($deposit->attachment);
            }
            $member = $this->member();
            $updateData['attachment'] = $request->file('attachment')
                ->store("deposits/{$member->id}", 'public');
        } elseif ($request->boolean('remove_attachment') && $deposit->attachment) {
            Storage::disk('public')->delete($deposit->attachment);
            $updateData['attachment'] = null;
        }

        $deposit->update($updateData);

        // No reallocation — deposit is still pending, not yet counted
        return redirect()->route('member.deposits.index')
            ->with('success', 'Deposit updated. It is still pending admin approval.');
    }

    public function destroy(Deposit $deposit)
    {
        $this->authorizeDeposit($deposit);
        abort_if(!$deposit->isPending(), 403, 'Only pending deposits can be deleted.');

        if ($deposit->attachment) {
            Storage::disk('public')->delete($deposit->attachment);
        }

        $deposit->delete();
        // No reallocation needed — pending deposits are never allocated

        return redirect()->route('member.deposits.index')
            ->with('success', 'Deposit cancelled.');
    }

    public function togglePermission()
    {
        $member = Member::findOrFail(auth()->user()->member_id);
        $member->update([
            'admin_deposit_permission' => !$member->admin_deposit_permission,
        ]);

        $status = $member->admin_deposit_permission ? 'enabled' : 'revoked';

        return back()->with('success', "Admin deposit permission {$status}.");
    }

    private function authorizeDeposit(Deposit $deposit): void
    {
        abort_if($deposit->member_id !== auth()->user()->member_id, 403);
    }

}

