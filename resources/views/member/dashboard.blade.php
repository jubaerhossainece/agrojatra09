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
