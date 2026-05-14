<x-layouts.admin title="Dashboard">

<div class="mt-4 space-y-6">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card
            label="Total Members"
            :value="$totalMembers"
            sub="Active members"
            color="green"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
        />
        <x-stat-card
            label="Total Shares"
            :value="$totalShares . ' shares'"
            :sub="'BDT ' . number_format($totalShareAmount)"
            color="blue"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'
        />
        <x-stat-card
            label="Total Deposited"
            :value="'৳ ' . number_format($totalDeposited)"
            sub="Bank deposits received"
            color="green"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
        />
        <x-stat-card
            label="Pending Balance"
            :value="'৳ ' . number_format($totalPending)"
            sub="Yet to be collected"
            color="amber"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Recent Deposits --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Recent Deposits</h2>
                <a href="{{ route('admin.deposits.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentDeposits as $deposit)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $deposit->member->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $deposit->deposit_date->format('d M Y') }} · {{ $deposit->bank_name }}</p>
                        </div>
                        <span class="text-sm font-semibold text-green-700">৳ {{ number_format($deposit->amount) }}</span>
                    </div>
                @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No deposits yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Members with pending payments --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Pending Payments</h2>
                <a href="{{ route('admin.members.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($pendingMembers as $member)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $member->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->total_shares }} share(s) · ৳ {{ number_format($member->total_amount) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-red-600">৳ {{ number_format($member->balance_due) }}</p>
                            <x-badge :status="$member->payment_status" />
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-4 text-sm text-gray-400">All payments are up to date! 🎉</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Quick actions --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.members.create') }}"
           class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Member
        </a>
        <a href="{{ route('admin.deposits.create') }}"
           class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Record Deposit
        </a>
        <a href="{{ route('admin.reports.index') }}"
           class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            View Reports
        </a>
    </div>

</div>

</x-layouts.admin>
