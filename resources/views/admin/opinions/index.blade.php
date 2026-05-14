<x-layouts.admin title="Opinions & Suggestions">

<div class="mt-4 space-y-4">

    <p class="text-sm text-gray-500">{{ $opinions->count() }} members shared their thoughts.</p>

    @forelse($opinions as $opinion)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr($opinion->member->full_name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $opinion->member->full_name }}</h3>
                            <p class="text-xs text-gray-400">{{ $opinion->member->profession }}</p>
                        </div>
                        <a href="{{ route('admin.members.show', $opinion->member) }}"
                           class="text-xs text-green-600 hover:text-green-700 font-medium">View Profile →</a>
                    </div>

                    @if($opinion->opinion)
                        <div class="mt-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Opinion</p>
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 italic">
                                "{{ $opinion->opinion }}"
                            </p>
                        </div>
                    @endif

                    @if($opinion->suggestion)
                        <div class="mt-3">
                            <p class="text-xs font-semibold text-amber-500 uppercase mb-1">Suggestion</p>
                            <p class="text-sm text-gray-700 bg-amber-50 rounded-lg p-3">
                                {{ $opinion->suggestion }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center text-gray-400">
            No opinions submitted yet.
        </div>
    @endforelse

</div>

</x-layouts.admin>
