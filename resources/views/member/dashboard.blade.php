<x-layouts.member title="My Dashboard">

<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-green-700 to-green-600 rounded-2xl p-6 text-white">
        <div class="flex items-center gap-4">
            @if($member->photo)
                <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->full_name }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-white/40 flex-shrink-0">
            @else
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                    {{ strtoupper(substr($member->full_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold">স্বাগতম, {{ explode(' ', $member->full_name)[0] }}!</h1>
                <p class="text-green-200 text-sm mt-0.5">
                    {{ $member->profession ?? 'Member' }}
                    @if($member->company_name) · {{ $member->company_name }}@endif
                </p>
                <p class="text-green-300 text-xs mt-1">🌿 Agrojatra09 — একসাথে এগিয়ে চলি</p>
            </div>
        </div>
    </div>

    {{-- Pending Share Change Request --}}
    @if($pendingShareChange)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-blue-900 text-sm">
                        Share Count Change Requested by
                        <span class="text-blue-700">{{ $pendingShareChange->requestedBy?->name ?? 'Admin' }}</span>
                    </p>
                    <p class="text-blue-700 text-sm mt-1">
                        Requesting to change your shares from
                        <strong>{{ $pendingShareChange->old_shares }}</strong> to <strong>{{ $pendingShareChange->new_shares }}</strong>
                        (৳ {{ number_format($pendingShareChange->new_shares * 2000) }} total).
                    </p>
                    @if($pendingShareChange->admin_note)
                        <p class="text-blue-600 text-xs mt-1">
                            <span class="font-medium">{{ $pendingShareChange->requestedBy?->name ?? 'Admin' }}:</span>
                            {{ $pendingShareChange->admin_note }}
                        </p>
                    @endif
                    <div class="mt-3 flex gap-2">
                        <form method="POST" action="{{ route('member.share-change.approve', $pendingShareChange) }}">
                            @csrf
                            <button x-data type="button"
                                    @click="$dispatch('open-confirm', {
                                        title: 'Approve Share Change',
                                        message: 'Your share count will be updated to {{ $pendingShareChange->new_shares }} shares (৳ {{ number_format($pendingShareChange->new_shares * 2000) }}).',
                                        confirmLabel: 'Approve',
                                        confirmClass: 'bg-green-600 hover:bg-green-700',
                                        target: $el.closest('form')
                                    })"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('member.share-change.reject', $pendingShareChange) }}">
                            @csrf
                            <button x-data type="button"
                                    @click="$dispatch('open-confirm', {
                                        title: 'Reject Share Change',
                                        message: 'The admin\'s share change request will be declined.',
                                        confirmLabel: 'Reject',
                                        confirmClass: 'bg-red-600 hover:bg-red-700',
                                        target: $el.closest('form')
                                    })"
                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 text-sm text-green-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Share Change History --}}
    @if($shareChangeHistory->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Share Change History</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold">Requested By</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold">Change</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold hidden sm:table-cell">Requested On</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold hidden md:table-cell">Reviewed On</th>
                        <th class="text-center px-4 py-2 text-xs text-gray-500 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($shareChangeHistory as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700 font-medium">{{ $req->requestedBy?->name ?? 'Admin' }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $req->old_shares }} → {{ $req->new_shares }}
                                <span class="text-xs text-gray-400 block">৳ {{ number_format($req->new_shares * 2000) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">
                                {{ $req->created_at->format('d M Y') }}
                                <span class="text-xs text-gray-400 block">{{ $req->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">
                                @if($req->reviewed_at)
                                    {{ $req->reviewed_at->format('d M Y') }}
                                    <span class="text-xs text-gray-400 block">{{ $req->reviewed_at->format('h:i A') }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span @class([
                                    'text-xs font-semibold px-2 py-0.5 rounded-full',
                                    'bg-green-100 text-green-700' => $req->status === 'approved',
                                    'bg-red-100 text-red-600'     => $req->status === 'rejected',
                                    'bg-amber-100 text-amber-700' => $req->status === 'pending',
                                    'bg-gray-100 text-gray-500'   => $req->status === 'cancelled',
                                ])>{{ ucfirst($req->status) }}</span>
                                @if($req->admin_note)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <span class="font-medium text-gray-500">{{ $req->requestedBy?->name ?? 'Admin' }}:</span>
                                        {{ $req->admin_note }}
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Share Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-green-700">{{ $member->total_shares }}</p>
            <p class="text-sm text-gray-500 mt-1">My Shares</p>
            <p class="text-xs text-gray-400">(1 share = BDT 2,000)</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <p class="text-2xl font-bold text-gray-900">৳ {{ number_format($member->total_deposited) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Deposited</p>
        </div>
        <div class="bg-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-50 rounded-xl border border-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-200 shadow-sm p-5 text-center">
            <p class="text-2xl font-bold text-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-700">
                ৳ {{ number_format($member->balance_due) }}
            </p>
            <p class="text-sm text-gray-500 mt-1">
                @if($member->balance_due > 0) Balance Due @else Fully Paid ✓ @endif
            </p>
            <x-badge :status="$member->payment_status" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Profile Summary --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">My Profile</h2>
                <a href="{{ route('member.profile') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">View Full →</a>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex items-start gap-2">
                    <span class="text-gray-400 w-24 flex-shrink-0">Phone</span>
                    <span class="text-gray-700 font-medium">{{ $member->phone }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-gray-400 w-24 flex-shrink-0">Email</span>
                    <span class="text-gray-700">{{ $member->email }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-gray-400 w-24 flex-shrink-0">Present</span>
                    <span class="text-gray-700">{{ $member->present_address }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-gray-400 w-24 flex-shrink-0">Permanent</span>
                    <span class="text-gray-700">{{ $member->permanent_address }}</span>
                </div>
                @if($member->blood_group)
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Blood</span>
                        <span class="text-red-600 font-semibold">{{ $member->blood_group }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Nominee --}}
        @if($member->nominee)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4">My Nominee</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Name</span>
                        <span class="font-semibold text-gray-900">{{ $member->nominee->nominee_name }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Relation</span>
                        <span class="text-gray-700">{{ $member->nominee->relationship }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Mobile</span>
                        <span class="text-gray-700">{{ $member->nominee->mobile }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Address</span>
                        <span class="text-gray-700">{{ $member->nominee->address }}</span>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Deposit History --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">My Deposit History</h2>
        </div>
        @if($member->deposits->count() > 0)
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold">Date</th>
                        <th class="text-right px-4 py-2 text-xs text-gray-500 font-semibold">Amount</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold hidden sm:table-cell">Bank</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold hidden md:table-cell">Reference</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500 font-semibold hidden md:table-cell">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($member->deposits as $deposit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $deposit->deposit_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-700">৳ {{ number_format($deposit->amount) }}</td>
                            <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $deposit->bank_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $deposit->bank_reference ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $deposit->receipt_number ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td class="px-4 py-2 text-xs font-semibold text-gray-600">Total</td>
                        <td class="px-4 py-2 text-right font-bold text-green-700">৳ {{ number_format($member->total_deposited) }}</td>
                        <td colspan="3" class="hidden md:table-cell"></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p class="px-6 py-6 text-sm text-gray-400 text-center">No deposits recorded yet.</p>
        @endif
    </div>

</div>

</x-layouts.member>
