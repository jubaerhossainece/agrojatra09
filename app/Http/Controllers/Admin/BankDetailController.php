<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankDetail;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    public function edit()
    {
        abort_if(!auth()->user()->isAccountant(), 403, 'Only the accountant can manage bank details.');

        $bankDetail = BankDetail::first();

        return view('admin.bank-details.edit', compact('bankDetail'));
    }

    public function update(Request $request)
    {
        abort_if(!auth()->user()->isAccountant(), 403, 'Only the accountant can manage bank details.');

        $data = $request->validate([
            'bank_name'      => 'required|string|max:255',
            'account_name'   => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'branch_name'    => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $bankDetail = BankDetail::first();
        $bankDetail ? $bankDetail->update($data) : BankDetail::create($data);

        return back()->with('success', 'Bank details updated successfully.');
    }
}
