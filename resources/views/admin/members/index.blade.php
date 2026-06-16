<x-layouts.admin title="Members">

<div class="mt-4 space-y-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">{{ $members->total() }} members registered</p>
        </div>
        <a href="{{ route('admin.members.create') }}"
           class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Member
        </a>
    </div>

    {{-- Search/Filter --}}
    @php
        $statusLabel = match(request('status')) { 'active' => 'Active', 'inactive' => 'Inactive', default => 'All Status' };
    @endphp
    <form method="GET" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, phone, email..."
               class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
        <div x-data="{ open: false, value: '{{ request('status', '') }}', label: '{{ $statusLabel }}' }"
             @click.outside="open = false" class="relative">
            <input type="hidden" name="status" :value="value">
            <button type="button" @click="open = !open"
                    class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors cursor-pointer whitespace-nowrap min-w-[130px] justify-between">
                <span x-text="label"></span>
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute z-50 mt-1 w-full min-w-[130px] bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                <div class="py-1">
                    <button type="button" @click="value = ''; label = 'All Status'; open = false"
                            :class="{ 'bg-green-50 text-green-700 font-semibold': value === '' }"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">All Status</button>
                    <button type="button" @click="value = 'active'; label = 'Active'; open = false"
                            :class="{ 'bg-green-50 text-green-700 font-semibold': value === 'active' }"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">Active</button>
                    <button type="button" @click="value = 'inactive'; label = 'Inactive'; open = false"
                            :class="{ 'bg-green-50 text-green-700 font-semibold': value === 'inactive' }"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">Inactive</button>
                </div>
            </div>
        </div>
        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Search
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.members.index') }}"
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Phone</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Profession</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Shares</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($members as $member)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($member->photo)
                                    <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->full_name }}"
                                         class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $member->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $member->phone }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $member->profession ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold text-gray-800">{{ $member->total_shares }}</span>
                            <span class="text-xs text-gray-500 block">৳ {{ number_format($member->total_amount) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <x-badge :status="$member->payment_status" />
                            <span class="text-xs text-gray-500 block mt-0.5">৳ {{ number_format($member->total_deposited) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <x-badge :status="$member->status" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.members.show', $member) }}"
                                   class="text-green-700 hover:text-green-900 font-medium text-xs px-2 py-1 rounded hover:bg-green-50 transition-colors">
                                    View
                                </a>
                                <a href="{{ route('admin.members.edit', $member) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.members.destroy', $member) }}">
                                    @csrf @method('DELETE')
                                    <button x-data type="button"
                                            @click="$dispatch('open-confirm', {
                                                title: 'Delete Member',
                                                message: 'This will permanently remove the member and all associated data.',
                                                confirmLabel: 'Delete',
                                                confirmClass: 'bg-red-600 hover:bg-red-700',
                                                target: $el.closest('form')
                                            })"
                                            class="text-red-600 hover:text-red-800 font-medium text-xs px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No members found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($members->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    </div>

</div>

</x-layouts.admin>
