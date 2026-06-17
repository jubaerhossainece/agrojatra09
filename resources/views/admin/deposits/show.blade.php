<x-layouts.admin title="Deposit Detail">

<div class="mt-4 max-w-2xl space-y-4">

    <a href="{{ route('admin.deposits.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Deposits</a>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">
                    <a href="{{ route('admin.members.show', $deposit->member) }}"
                       class="hover:text-green-700">{{ $deposit->member->full_name }}</a>
                </h1>
                <p class="text-sm text-gray-500">Recorded by {{ $deposit->recorder?->name ?? '—' }}
                    · {{ $deposit->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <p class="text-2xl font-bold text-green-700">৳ {{ number_format($deposit->amount) }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-4">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Deposit Date</p>
                <p class="font-medium text-gray-800">{{ $deposit->deposit_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Bank Name</p>
                <p class="font-medium text-gray-800">{{ $deposit->bank_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Bank Reference / Txn ID</p>
                <p class="font-medium text-gray-800">{{ $deposit->bank_reference ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Receipt Number</p>
                <p class="font-medium text-gray-800">{{ $deposit->receipt_number ?? '—' }}</p>
            </div>
            @if($deposit->note)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">Note</p>
                    <p class="text-gray-700">{{ $deposit->note }}</p>
                </div>
            @endif
        </div>

        @if($deposit->attachment)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Attachment</p>
                @if($deposit->attachmentIsImage())
                    <a href="{{ $deposit->attachmentUrl() }}" target="_blank">
                        <img src="{{ $deposit->attachmentUrl() }}" alt="Deposit attachment"
                             class="max-h-96 rounded-lg border border-gray-200 object-contain">
                    </a>
                @else
                    <a href="{{ $deposit->attachmentUrl() }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                        </svg>
                        Download PDF
                    </a>
                @endif
            </div>
        @else
            <div class="border-t border-gray-100 pt-4">
                <p class="text-sm text-gray-400 italic">No attachment uploaded.</p>
            </div>
        @endif
    </div>

    @if(auth()->user()->canDeleteDeposits())
        <form method="POST" action="{{ route('admin.deposits.destroy', $deposit) }}">
            @csrf @method('DELETE')
            <button x-data type="button"
                    @click="$dispatch('open-confirm', {
                        title: 'Delete Deposit',
                        message: 'This deposit record will be permanently removed.',
                        confirmLabel: 'Delete',
                        confirmClass: 'bg-red-600 hover:bg-red-700',
                        target: $el.closest('form')
                    })"
                    class="text-red-600 hover:text-red-800 text-sm font-medium px-4 py-2 rounded-lg border border-red-200 hover:bg-red-50 transition-colors">
                Delete Deposit
            </button>
        </form>
    @endif

</div>

</x-layouts.admin>
