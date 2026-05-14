<x-layouts.admin title="Record Deposit">

<div class="mt-4 max-w-2xl">

    <div class="mb-4">
        <a href="{{ route('admin.deposits.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Deposits</a>
    </div>

    {{-- Permission notice --}}
    @if($members->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-3 items-start mb-4">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold text-amber-800 text-sm">No members have granted permission</p>
                <p class="text-amber-700 text-sm mt-0.5">
                    Members must enable "Allow Admin to Manage My Deposits" from their member panel before you can record deposits on their behalf.
                </p>
            </div>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3 items-start mb-4">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="text-blue-800 text-sm">
                Only showing <strong>{{ $members->count() }}</strong> member(s) who have granted admin deposit permission.
                Members can enable this from their deposits page.
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.deposits.store') }}" class="space-y-4"
          x-data="depositForm()" x-init="init()">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-gray-800">Deposit Details</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Member <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-400 font-normal ml-1">(permission-granted only)</span>
                </label>
                <select name="member_id" required x-model="selectedMemberId" @change="fetchMemberInfo"
                        class="w-full border {{ $errors->has('member_id') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none"
                        {{ $members->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Select Member</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}"
                                data-amount="{{ $m->total_amount }}"
                                data-deposited="{{ $m->total_deposited }}"
                                {{ old('member_id') == $m->id || (isset($selectedMember) && $selectedMember->id == $m->id) ? 'selected' : '' }}>
                            {{ $m->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('member_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Balance summary --}}
            <div x-show="selectedMemberId" x-transition class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm">
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div>
                        <p class="text-xs text-gray-500">Total Amount</p>
                        <p class="font-bold text-gray-800">৳ <span x-text="totalAmount.toLocaleString()"></span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Deposited</p>
                        <p class="font-bold text-green-700">৳ <span x-text="deposited.toLocaleString()"></span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Balance Due</p>
                        <p class="font-bold text-red-600">৳ <span x-text="balanceDue.toLocaleString()"></span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (BDT) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="1" required
                           class="w-full border {{ $errors->has('amount') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Date <span class="text-red-500">*</span></label>
                    <input type="date" name="deposit_date" value="{{ old('deposit_date', date('Y-m-d')) }}" required
                           class="w-full border {{ $errors->has('deposit_date') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Reference / Transaction ID</label>
                    <input type="text" name="bank_reference" value="{{ old('bank_reference') }}"
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
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">{{ old('note') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" {{ $members->isEmpty() ? 'disabled' : '' }}
                    class="bg-green-700 hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Record Deposit
            </button>
            <a href="{{ route('admin.deposits.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function depositForm() {
    return {
        selectedMemberId: @json(old('member_id', isset($selectedMember) ? $selectedMember->id : '')),
        totalAmount: @json(isset($selectedMember) ? (float) $selectedMember->total_amount : 0),
        deposited: @json(isset($selectedMember) ? (float) $selectedMember->total_deposited : 0),
        get balanceDue() { return this.totalAmount - this.deposited; },
        init() { if (this.selectedMemberId) this.fetchMemberInfo(); },
        fetchMemberInfo() {
            const sel = document.querySelector('select[name="member_id"]');
            const opt = sel.options[sel.selectedIndex];
            this.totalAmount = parseFloat(opt.dataset.amount || 0);
            this.deposited   = parseFloat(opt.dataset.deposited || 0);
        }
    }
}
</script>

</x-layouts.admin>
