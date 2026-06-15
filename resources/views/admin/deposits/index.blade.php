<x-layouts.admin title="Deposits">

<div class="mt-4 space-y-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $deposits->total() }} total deposits</p>
        <a href="{{ route('admin.deposits.create') }}"
           class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Record Deposit
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-wrap gap-3">
        <select name="member_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
            <option value="">All Members</option>
            @foreach($members as $m)
                <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>{{ $m->full_name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['member_id', 'date_from', 'date_to']))
            <a href="{{ route('admin.deposits.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Member</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Bank</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Reference</th>
                    <th class="px-4 py-3 hidden lg:table-cell"></th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($deposits as $deposit)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.members.show', $deposit->member) }}"
                               class="font-medium text-gray-900 hover:text-green-700">
                                {{ $deposit->member->full_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700">
                            ৳ {{ number_format($deposit->amount) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">
                            {{ $deposit->deposit_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $deposit->bank_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $deposit->bank_reference ?? '—' }}</td>
                        <td class="px-4 py-3 hidden lg:table-cell">
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
                                <a href="{{ route('admin.deposits.show', $deposit) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                    View
                                </a>
                                <form method="POST" action="{{ route('admin.deposits.destroy', $deposit) }}"
                                      onsubmit="return confirm('Delete this deposit?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 font-medium text-xs px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">No deposits found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($deposits->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>
</div>

</x-layouts.admin>
