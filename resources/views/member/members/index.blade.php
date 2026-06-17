<x-layouts.member title="All Members">

<div class="max-w-4xl space-y-4">

    <div class="flex items-center justify-between">
        <h1 class="text-lg font-bold text-gray-900">Members <span class="text-gray-400 font-normal text-base">({{ $members->total() }})</span></h1>
    </div>

    {{-- Search --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or email..."
               x-data
               x-init="if ($el.value) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
               x-on:input.debounce.500ms="$el.form.submit()"
               class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Search
        </button>
        @if(request()->hasAny(['search']))
            <a href="{{ route('member.members.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Clear
            </a>
        @endif
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($members as $member)
            <a href="{{ route('member.members.show', $member) }}"
               class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-green-300 hover:shadow-md transition-all group">

                <div class="flex items-center gap-3 mb-4">
                    @if($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->full_name }}"
                             class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xl font-bold flex-shrink-0">
                            {{ strtoupper(substr($member->full_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors truncate">{{ $member->full_name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $member->company_name ?? $member->profession ?? '—' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs border-t border-gray-100 pt-3">
                    <div>
                        <p class="font-bold text-gray-800">{{ $member->total_shares }}</p>
                        <p class="text-gray-400">Shares</p>
                    </div>
                    <div>
                        <p class="font-bold text-green-700">৳ {{ number_format($member->total_deposited) }}</p>
                        <p class="text-gray-400">Paid</p>
                    </div>
                    <div>
                        <p class="font-bold text-{{ $member->balance_due > 0 ? 'amber-600' : 'green-600' }}">
                            ৳ {{ number_format($member->balance_due) }}
                        </p>
                        <p class="text-gray-400">Due</p>
                    </div>
                </div>

                <div class="mt-3 flex justify-between items-center">
                    <x-badge :status="$member->payment_status" />
                    @if($member->blood_group)
                        <span class="text-xs text-red-500 font-medium">🩸 {{ $member->blood_group }}</span>
                    @endif
                </div>
            </a>
        @empty
            <p class="col-span-full text-center text-gray-400 py-8">No members found.</p>
        @endforelse
    </div>

    @if($members->hasPages())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3">
            {{ $members->links() }}
        </div>
    @endif

</div>

</x-layouts.member>
