<x-layouts.member title="Record Deposit">

<div class="max-w-2xl space-y-4">

    <div class="flex items-center justify-between">
        <a href="{{ route('member.deposits.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Deposits</a>
    </div>

    {{-- Balance info --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 grid grid-cols-3 gap-2 text-center text-sm">
        <div>
            <p class="text-xs text-gray-500">Committed</p>
            <p class="font-bold text-gray-800">৳ {{ number_format($member->total_amount) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Deposited</p>
            <p class="font-bold text-green-700">৳ {{ number_format($member->total_deposited) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Balance Due</p>
            <p class="font-bold text-{{ $member->balance_due > 0 ? 'amber-600' : 'green-700' }}">
                ৳ {{ number_format($member->balance_due) }}
            </p>
        </div>
    </div>

    <x-bank-details-card />

    <form method="POST" action="{{ route('member.deposits.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-gray-800">New Bank Deposit</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (BDT) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="1" required
                           class="w-full border {{ $errors->has('amount') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Date <span class="text-red-500">*</span></label>
                    <input type="text" data-datepicker name="deposit_date" value="{{ old('deposit_date', date('Y-m-d')) }}" required autocomplete="off"
                           class="w-full border {{ $errors->has('deposit_date') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    @error('deposit_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                           placeholder="e.g. Dutch-Bangla Bank"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Reference / Transaction ID</label>
                    <input type="text" name="bank_reference" value="{{ old('bank_reference') }}"
                           placeholder="e.g. TXN123456"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Number</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                <textarea name="note" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none"
                          placeholder="Optional note about this payment...">{{ old('note') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment</label>
                <p class="text-xs text-gray-400 mb-1">Screenshot or transaction PDF — JPG, PNG, PDF · max 5 MB</p>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                @error('attachment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Save Deposit
            </button>
            <a href="{{ route('member.deposits.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>

</div>

</x-layouts.member>
