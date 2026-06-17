<x-layouts.admin :title="$member->full_name">

<div class="mt-4 space-y-6">

    {{-- Back + Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.members.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Back to Members
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.deposits.create', ['member_id' => $member->id]) }}"
               class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                + Add Deposit
            </a>
            <a href="{{ route('admin.members.edit', $member) }}"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                Edit Member
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <div class="xl:col-span-2 space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-start gap-5">
                    @if($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->full_name }}"
                             class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                    @else
                        <div class="w-20 h-20 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-3xl font-bold flex-shrink-0">
                            {{ strtoupper(substr($member->full_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $member->full_name }}</h2>
                                <p class="text-gray-500 text-sm">{{ $member->profession ?? 'N/A' }}
                                    @if($member->company_name) · {{ $member->company_name }}@endif
                                </p>
                            </div>
                            <x-badge :status="$member->status" />
                        </div>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $member->phone }}
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $member->email }}
                            </div>
                            @if($member->blood_group)
                            <div class="flex items-center gap-2 text-gray-600">
                                <span class="text-red-500 font-bold">🩸</span>
                                Blood: {{ $member->blood_group }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-5 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Permanent Address</p>
                        <p class="text-gray-700">{{ $member->permanent_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Present Address</p>
                        <p class="text-gray-700">{{ $member->present_address }}</p>
                    </div>
                    @if($member->company_address)
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Company Address</p>
                        <p class="text-gray-700">{{ $member->company_address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Shares Summary --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Share Investment</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-green-700">{{ $member->total_shares }}</p>
                        <p class="text-xs text-gray-500 mt-1">Shares</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-blue-700">৳ {{ number_format($member->total_deposited) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Deposited</p>
                    </div>
                    <div class="bg-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-700">
                            ৳ {{ number_format($member->balance_due) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Balance Due</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-600">Total commitment: <strong>৳ {{ number_format($member->total_amount) }}</strong></span>
                    <x-badge :status="$member->payment_status" />
                </div>
            </div>

            {{-- Share Change Request --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Request Share Count Change</h3>

                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if($pendingShareChange)
                    @php $isSelf = auth()->user()->member_id === $member->id; @endphp
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm space-y-2">
                        <p class="font-semibold text-amber-800">
                            {{ $isSelf ? 'Pending your approval' : 'Awaiting member approval' }}
                        </p>
                        <p class="text-amber-700">
                            Requested change: <strong>{{ $pendingShareChange->old_shares }}</strong> → <strong>{{ $pendingShareChange->new_shares }}</strong> shares
                            (৳ {{ number_format($pendingShareChange->new_shares * 2000) }})
                        </p>
                        @if($pendingShareChange->admin_note)
                            <p class="text-amber-600 text-xs">Note: {{ $pendingShareChange->admin_note }}</p>
                        @endif
                        <p class="text-amber-400 text-xs">Sent {{ $pendingShareChange->created_at->diffForHumans() }}</p>

                        @if($isSelf && $pendingShareChange->requested_by !== auth()->id())
                            <div class="flex gap-2 pt-1">
                                <form method="POST" action="{{ route('admin.share-changes.approve', $pendingShareChange) }}">
                                    @csrf
                                    <button type="submit"
                                            class="bg-green-700 hover:bg-green-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.share-changes.reject', $pendingShareChange) }}">
                                    @csrf
                                    <button x-data type="button"
                                            @click="$dispatch('open-confirm', {
                                                title: 'Reject Share Change',
                                                message: 'Are you sure you want to reject this share change request?',
                                                confirmLabel: 'Reject',
                                                confirmClass: 'bg-red-600 hover:bg-red-700',
                                                target: $el.closest('form')
                                            })"
                                            class="bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @elseif($isSelf && $pendingShareChange->requested_by === auth()->id())
                            <p class="text-xs text-amber-500 italic">You initiated this request — another admin must send a new one for you to approve.</p>
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.members.share-change.store', $member) }}" class="space-y-3">
                        @csrf
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">New Share Count <span class="text-red-500">*</span></label>
                                <input type="number" name="new_shares"
                                       value="{{ old('new_shares', $member->total_shares) }}"
                                       min="1" max="100" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                <p class="text-xs text-gray-400 mt-0.5">Current: {{ $member->total_shares }} shares (৳ {{ number_format($member->total_amount) }})</p>
                            </div>
                            <div class="flex items-end pb-6">
                                <button x-data type="button"
                                        @click="$dispatch('open-confirm', {
                                            title: 'Send Share Change Request',
                                            message: 'This will notify {{ addslashes($member->full_name) }} and ask them to approve the new share count. Continue?',
                                            confirmLabel: 'Send Request',
                                            confirmClass: 'bg-blue-600 hover:bg-blue-700',
                                            target: $el.closest('form')
                                        })"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                                    Send Request
                                </button>
                            </div>
                        </div>
                        <input type="text" name="admin_note" placeholder="Optional note to member..."
                               value="{{ old('admin_note') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <p class="text-xs text-gray-400">The member will be notified and must approve this change before it takes effect.</p>
                    </form>
                @endif

                {{-- History --}}
                @if($shareChangeHistory->isNotEmpty())
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Request History</h4>
                        <div class="space-y-2">
                            @foreach($shareChangeHistory as $req)
                                <div class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 text-sm">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="space-y-0.5">
                                            <p class="text-gray-800 font-medium">
                                                {{ $req->old_shares }} → {{ $req->new_shares }} shares
                                                <span class="text-gray-400 font-normal">(৳ {{ number_format($req->new_shares * 2000) }})</span>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Requested by <span class="font-medium text-gray-700">{{ $req->requestedBy?->name ?? 'Admin' }}</span>
                                                on {{ $req->created_at->format('d M Y, h:i A') }}
                                            </p>
                                            @if($req->reviewed_at)
                                                <p class="text-xs text-gray-500">
                                                    {{ ucfirst($req->status) }} by member on {{ $req->reviewed_at->format('d M Y, h:i A') }}
                                                </p>
                                            @endif
                                            @if($req->admin_note)
                                                <p class="text-xs text-gray-400 italic">Admin note: {{ $req->admin_note }}</p>
                                            @endif
                                            @if($req->member_note)
                                                <p class="text-xs text-gray-400 italic">Member note: {{ $req->member_note }}</p>
                                            @endif
                                        </div>
                                        <span @class([
                                            'text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap flex-shrink-0',
                                            'bg-green-100 text-green-700' => $req->status === 'approved',
                                            'bg-red-100 text-red-600'     => $req->status === 'rejected',
                                            'bg-amber-100 text-amber-700' => $req->status === 'pending',
                                            'bg-gray-100 text-gray-500'   => $req->status === 'cancelled',
                                        ])>{{ ucfirst($req->status) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Deposit History --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Deposit History</h3>
                    <a href="{{ route('admin.deposits.create', ['member_id' => $member->id]) }}"
                       class="text-sm text-green-700 hover:text-green-900 font-medium">+ Add</a>
                </div>
                @if($member->deposits->count() > 0)
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold">Date</th>
                                <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold">Amount</th>
                                <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold hidden sm:table-cell">Bank</th>
                                <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold hidden md:table-cell">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($member->deposits as $deposit)
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">{{ $deposit->deposit_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 font-semibold text-green-700">৳ {{ number_format($deposit->amount) }}</td>
                                    <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $deposit->bank_name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $deposit->bank_reference ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-6 py-4 text-sm text-gray-400">No deposits recorded yet.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar: Nominee & Opinion --}}
        <div class="space-y-4">
            @if($member->nominee)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Nominee</h3>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Name</p>
                            <p class="font-medium text-gray-800">{{ $member->nominee->nominee_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Relationship</p>
                            <p class="text-gray-700">{{ $member->nominee->relationship }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Mobile</p>
                            <p class="text-gray-700">{{ $member->nominee->mobile }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Address</p>
                            <p class="text-gray-700">{{ $member->nominee->address }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($member->groupOpinion && ($member->groupOpinion->opinion || $member->groupOpinion->suggestion))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Opinion & Suggestion</h3>
                    @if($member->groupOpinion->opinion)
                        <div class="mb-3">
                            <p class="text-xs text-gray-400 mb-1">Opinion</p>
                            <p class="text-sm text-gray-700 italic">"{{ $member->groupOpinion->opinion }}"</p>
                        </div>
                    @endif
                    @if($member->groupOpinion->suggestion)
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Suggestion</p>
                            <p class="text-sm text-gray-700 italic">"{{ $member->groupOpinion->suggestion }}"</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>

</x-layouts.admin>
