@php $bankDetail = \App\Models\BankDetail::first(); @endphp

@if($bankDetail)
<div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 text-sm text-blue-900">
    <p class="font-semibold mb-2 flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 10h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2zM7 15h2"/>
        </svg>
        Send your deposit to this account
    </p>
    <dl class="grid grid-cols-2 gap-x-4 gap-y-1">
        <dt class="text-blue-700">Bank</dt>
        <dd class="font-medium">{{ $bankDetail->bank_name }}</dd>
        <dt class="text-blue-700">Account Name</dt>
        <dd class="font-medium">{{ $bankDetail->account_name }}</dd>
        <dt class="text-blue-700">Account Number</dt>
        <dd class="font-medium">{{ $bankDetail->account_number }}</dd>
        @if($bankDetail->branch_name)
            <dt class="text-blue-700">Branch</dt>
            <dd class="font-medium">{{ $bankDetail->branch_name }}</dd>
        @endif
        @if($bankDetail->routing_number)
            <dt class="text-blue-700">Routing Number</dt>
            <dd class="font-medium">{{ $bankDetail->routing_number }}</dd>
        @endif
    </dl>
    @if($bankDetail->notes)
        <p class="mt-2 pt-2 border-t border-blue-200 text-blue-800">{{ $bankDetail->notes }}</p>
    @endif
</div>
@endif
