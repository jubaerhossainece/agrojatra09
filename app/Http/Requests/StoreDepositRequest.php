<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'member_id'      => ['required', 'exists:members,id'],
            'share_id'       => ['nullable', 'exists:shares,id'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'deposit_date'   => ['required', 'date'],
            'bank_name'      => ['required', 'string', 'max:255'],
            'bank_reference' => ['required', 'string', 'max:255'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
            'note'           => ['nullable', 'string'],
            'attachment'     => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
        ];
    }
}
