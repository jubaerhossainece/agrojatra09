<x-layouts.member title="My Deposits">

<div class="max-w-4xl space-y-6">

    {{-- Permission Toggle Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-gray-800">Admin Deposit Permission</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Allow the admin to record or update deposits on your behalf.
                    @if($member->admin_deposit_permission)
                        <span class="text-green-600 font-medium">Currently enabled.</span>
                    @else
                        <span class="text-gray-400 font-medium">Currently disabled.</span>
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('member.deposits.toggle-permission') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors
                               {{ $member->admin_deposit_permission
                                   ? 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100'
                                   : 'bg-green-50 border border-green-200 text-green-700 hover:bg-green-100' }}">
                    @if($member->admin_deposit_permission)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Revoke Admin Permission
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Allow Admin to Manage My Deposits
                    @endif
                </button>
            </form>
        </div>
    </div>

    {{-- Balance Summary --}}
    @php
        $pendingAmount = $deposits->where('status', 'pending')->sum('amount');
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-gray-900">৳ {{ number_format($member->total_amount) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Commitment</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-green-700">৳ {{ number_format($member->total_deposited) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Approved Deposits</p>
            @if($pendingAmount > 0)
                <p class="text-xs text-amber-600 mt-0.5">+ ৳{{ number_format($pendingAmount) }} pending</p>
            @endif
        </div>
        <div class="bg-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-50 rounded-xl border border-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-700">৳ {{ number_format($member->balance_due) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Balance Due</p>
        </div>
    </div>

    {{-- Deposits Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Deposit History ({{ $deposits->count() }})</h2>
            <a href="{{ route('member.deposits.create') }}"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Deposit
            </a>
        </div>

        @if($deposits->count() > 0)
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Bank</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Reference</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell"></th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($deposits as $deposit)
                        <tr class="{{ $deposit->isPending() ? 'bg-amber-50 hover:bg-amber-100' : 'hover:bg-gray-50' }} transition-colors">
                            <td class="px-4 py-3 text-gray-700">{{ $deposit->deposit_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $deposit->isApproved() ? 'text-green-700' : ($deposit->isRejected() ? 'text-gray-400 line-through' : 'text-amber-700') }}">
                                ৳ {{ number_format($deposit->amount) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($deposit->isPending())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                                @elseif($deposit->isApproved())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Approved</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $deposit->bank_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $deposit->bank_reference ?? '—' }}</td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if($deposit->attachment)
                                    <a href="{{ $deposit->attachmentUrl() }}" target="_blank"
                                       title="View attachment" class="text-blue-500 hover:text-blue-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($deposit->isPending())
                                        <a href="{{ route('member.deposits.edit', $deposit) }}"
                                           class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('member.deposits.destroy', $deposit) }}">
                                            @csrf @method('DELETE')
                                            <button x-data type="button"
                                                    @click="$dispatch('open-confirm', {
                                                        title: 'Cancel Deposit',
                                                        message: 'This pending deposit will be removed.',
                                                        confirmLabel: 'Cancel it',
                                                        confirmClass: 'bg-red-600 hover:bg-red-700',
                                                        target: $el.closest('form')
                                                    })"
                                                    class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                                Cancel
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td class="px-4 py-2 text-xs font-semibold text-gray-600">Approved total</td>
                        <td class="px-4 py-2 text-right font-bold text-green-700">৳ {{ number_format($deposits->where('status','approved')->sum('amount')) }}</td>
                        <td colspan="5" class="hidden md:table-cell"></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="px-6 py-10 text-center">
                <p class="text-gray-400 text-sm mb-3">No deposits recorded yet.</p>
                <a href="{{ route('member.deposits.create') }}"
                   class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Record Your First Deposit
                </a>
            </div>
        @endif
    </div>

</div>

</x-layouts.member>
