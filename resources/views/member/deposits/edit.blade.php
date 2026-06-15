<x-layouts.member title="Edit Deposit">

<div class="max-w-2xl space-y-4">

    <a href="{{ route('member.deposits.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Deposits</a>

    <form method="POST" action="{{ route('member.deposits.update', $deposit) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-gray-800">Edit Deposit</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (BDT) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $deposit->amount) }}" min="1" step="1" required
                           class="w-full border {{ $errors->has('amount') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Date <span class="text-red-500">*</span></label>
                    <input type="date" name="deposit_date" value="{{ old('deposit_date', $deposit->deposit_date->format('Y-m-d')) }}" required
                           class="w-full border {{ $errors->has('deposit_date') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    @error('deposit_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $deposit->bank_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Reference / Transaction ID</label>
                    <input type="text" name="bank_reference" value="{{ old('bank_reference', $deposit->bank_reference) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Number</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number', $deposit->receipt_number) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                <textarea name="note" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">{{ old('note', $deposit->note) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment</label>
                @if($deposit->attachment)
                    <div class="mb-2 p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-3">
                        @if($deposit->attachmentIsImage())
                            <img src="{{ $deposit->attachmentUrl() }}" alt="Attachment"
                                 class="h-16 w-16 object-cover rounded border border-gray-200">
                        @else
                            <svg class="w-10 h-10 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                            </svg>
                        @endif
                        <div class="flex-1 min-w-0">
                            <a href="{{ $deposit->attachmentUrl() }}" target="_blank"
                               class="text-sm text-blue-600 hover:underline truncate block">View current attachment</a>
                            <label class="flex items-center gap-2 mt-1 cursor-pointer">
                                <input type="checkbox" name="remove_attachment" value="1"
                                       class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-xs text-red-600">Remove this attachment</span>
                            </label>
                        </div>
                    </div>
                @endif
                <p class="text-xs text-gray-400 mb-1">Replace with new file — JPG, PNG, PDF · max 5 MB</p>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                @error('attachment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Update Deposit
            </button>
            <a href="{{ route('member.deposits.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>

</div>

</x-layouts.member>
