<x-layouts.admin title="Deposits">

<div class="mt-4 space-y-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $deposits->total() }} deposit(s)</p>
        <a href="{{ route('admin.deposits.create') }}"
           class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Record Deposit
        </a>
    </div>

    {{-- Status tabs --}}
    @php
        $tabs = [
            null       => 'All',
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    @endphp
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
        @foreach($tabs as $val => $label)
            <a href="{{ route('admin.deposits.index', array_merge(request()->except(['status', 'page']), $val ? ['status' => $val] : [])) }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors
                      {{ $status === $val || ($val === null && $status === null)
                          ? 'bg-white text-gray-900 shadow-sm'
                          : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
                @if($val === 'pending' && $pendingCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold bg-red-500 text-white rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    @php
        $selectedMemberLabel = 'All Members';
        if (request('member_id')) {
            $found = $members->firstWhere('id', request('member_id'));
            if ($found) $selectedMemberLabel = $found->full_name;
        }
    @endphp
    <form method="GET" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-wrap gap-3">
        @if($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <div x-data="{ open: false, value: '{{ request('member_id', '') }}', label: '{{ addslashes($selectedMemberLabel) }}' }"
             @click.outside="open = false" class="relative">
            <input type="hidden" name="member_id" :value="value">
            <button type="button" @click="open = !open"
                    class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors cursor-pointer whitespace-nowrap min-w-[160px] justify-between">
                <span x-text="label" class="truncate max-w-[140px]"></span>
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open"
                 style="display:none"
                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute z-50 mt-1 w-56 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                <div class="py-1 max-h-56 overflow-y-auto">
                    <button type="button" @click="value = ''; label = 'All Members'; open = false"
                            :class="{ 'bg-green-50 text-green-700 font-semibold': value === '' }"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">All Members</button>
                    @foreach($members as $m)
                        <button type="button" @click="value = '{{ $m->id }}'; label = '{{ addslashes($m->full_name) }}'; open = false"
                                :class="{ 'bg-green-50 text-green-700 font-semibold': value === '{{ $m->id }}' }"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">{{ $m->full_name }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <input type="text" data-datepicker name="date_from" value="{{ request('date_from') }}" placeholder="From date" autocomplete="off"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
        <input type="text" data-datepicker name="date_to" value="{{ request('date_to') }}" placeholder="To date" autocomplete="off"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['member_id', 'date_from', 'date_to']))
            <a href="{{ route('admin.deposits.index', $status ? ['status' => $status] : []) }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Clear
            </a>
        @endif
    </form>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Member</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Bank</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 hidden lg:table-cell"></th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($deposits as $deposit)
                    <tr class="{{ $deposit->isPending() ? 'bg-amber-50 hover:bg-amber-100' : 'hover:bg-gray-50' }} transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.members.show', $deposit->member) }}"
                               class="font-medium text-gray-900 hover:text-green-700">
                                {{ $deposit->member->full_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $deposit->isApproved() ? 'text-green-700' : ($deposit->isRejected() ? 'text-gray-400 line-through' : 'text-amber-700') }}">
                            ৳ {{ number_format($deposit->amount) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">
                            {{ $deposit->deposit_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $deposit->bank_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($deposit->isPending())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                            @elseif($deposit->isApproved())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Approved</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Rejected</span>
                            @endif
                        </td>
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
                                {{-- Approve / Reject: only accountant or president --}}
                                @if($deposit->isPending() && auth()->user()->canApproveDeposits())
                                    <form method="POST" action="{{ route('admin.deposits.approve', $deposit) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-green-700 hover:text-green-900 font-semibold text-xs px-2 py-1 rounded bg-green-50 hover:bg-green-100 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.deposits.reject', $deposit) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 font-semibold text-xs px-2 py-1 rounded bg-red-50 hover:bg-red-100 transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                @elseif($deposit->isPending())
                                    <span class="text-xs text-gray-400 italic">Pending</span>
                                @endif
                                <a href="{{ route('admin.deposits.show', $deposit) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                    View
                                </a>
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
                                                class="text-red-600 hover:text-red-800 font-medium text-xs px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No deposits found.</td>
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
