<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $memberId = $this->route('member');

        return [
            'full_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', Rule::unique('members', 'email')->ignore($memberId)],
            'phone'             => ['required', 'string', 'max:20'],
            'permanent_address' => ['required', 'string'],
            'present_address'   => ['required', 'string'],
            'profession'        => ['nullable', 'string', 'max:255'],
            'company_name'      => ['nullable', 'string', 'max:255'],
            'company_address'   => ['nullable', 'string', 'max:255'],
            'blood_group'       => ['nullable', 'string', 'max:10'],
            'photo'             => ['nullable', 'image', 'max:2048'],
            'status'            => ['required', 'in:active,inactive'],
            'nominee_name'      => ['required', 'string', 'max:255'],
            'relationship'      => ['required', 'string', 'max:100'],
            'nominee_mobile'    => ['required', 'string', 'max:20'],
            'nominee_address'   => ['required', 'string'],
        ];
    }
}
