<x-layouts.admin title="Bank Details">

<div class="mt-4 max-w-2xl space-y-6">

    <p class="text-sm text-gray-500">These details are shown to members on the deposit form so they know where to send money. Changes take effect immediately.</p>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.bank-details.update') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name <span class="text-red-500">*</span></label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $bankDetail?->bank_name) }}" required
                       class="w-full border {{ $errors->has('bank_name') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                @error('bank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Holder Name <span class="text-red-500">*</span></label>
                <input type="text" name="account_name" value="{{ old('account_name', $bankDetail?->account_name) }}" required
                       class="w-full border {{ $errors->has('account_name') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                @error('account_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number <span class="text-red-500">*</span></label>
                <input type="text" name="account_number" value="{{ old('account_number', $bankDetail?->account_number) }}" required
                       class="w-full border {{ $errors->has('account_number') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                @error('account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Branch Name</label>
                <input type="text" name="branch_name" value="{{ old('branch_name', $bankDetail?->branch_name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Routing Number</label>
                <input type="text" name="routing_number" value="{{ old('routing_number', $bankDetail?->routing_number) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
            <textarea name="notes" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none"
                      placeholder="Any extra instructions for members, e.g. reference format to use">{{ old('notes', $bankDetail?->notes) }}</textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Save Bank Details
            </button>
        </div>
    </form>

</div>

</x-layouts.admin>
