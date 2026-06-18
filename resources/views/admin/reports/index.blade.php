<x-layouts.admin title="Reports">

<div class="mt-4 space-y-6">

    {{-- Print Button --}}
    <div class="flex justify-end">
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Report
        </button>
    </div>

    {{-- Group Totals --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $totalShares }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Shares</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <p class="text-2xl font-bold text-gray-900">৳ {{ number_format($totalShareAmount) }}</p>
            <p class="text-sm text-gray-500 mt-1">Committed Amount</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-200 shadow-sm p-5 text-center">
            <p class="text-2xl font-bold text-green-700">৳ {{ number_format($totalDeposited) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Deposited</p>
        </div>
        <div class="bg-amber-50 rounded-xl border border-amber-200 shadow-sm p-5 text-center">
            <p class="text-2xl font-bold text-amber-700">৳ {{ number_format($totalPending) }}</p>
            <p class="text-sm text-gray-500 mt-1">Pending Balance</p>
        </div>
    </div>

    {{-- Member-wise Summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-gray-800">Member-wise Investment Summary</h2>
                <p class="text-sm text-gray-500 mt-0.5">Agrojatra09 · Batch 2009 · Magura</p>
            </div>

            {{-- Status tabs --}}
            @php
                $tabs = [
                    null      => 'All',
                    'paid'    => 'Paid',
                    'partial' => 'Partial',
                    'pending' => 'Pending',
                ];
            @endphp
            <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit print:hidden">
                @foreach($tabs as $val => $tabLabel)
                    <a href="{{ route('admin.reports.index', $val ? ['status' => $val] : []) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors
                              {{ $status === $val ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $tabLabel }}
                        @if($val && $statusCounts[$val] > 0)
                            <span class="ml-1 text-xs text-gray-400">({{ $statusCounts[$val] }})</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Shares</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Share Amount</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Deposited</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Balance Due</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($members as $i => $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.members.show', $member) }}"
                               class="font-medium text-gray-900 hover:text-green-700">
                                {{ $member->full_name }}
                            </a>
                            <p class="text-xs text-gray-400">{{ $member->profession }}</p>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $member->total_shares }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">৳ {{ number_format($member->total_amount) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700">৳ {{ number_format($member->total_deposited) }}</td>
                        <td class="px-4 py-3 text-right {{ $member->balance_due > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                            ৳ {{ number_format($member->balance_due) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <x-badge :status="$member->payment_status" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">
                            No {{ $status ? strtolower($tabs[$status]) . ' ' : '' }}members found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-semibold">
                <tr>
                    <td colspan="2" class="px-4 py-3 text-gray-700">TOTAL ({{ $members->count() }} Members)</td>
                    <td class="px-4 py-3 text-center text-gray-800">{{ $tableTotals['shares'] }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">৳ {{ number_format($tableTotals['amount']) }}</td>
                    <td class="px-4 py-3 text-right text-green-700">৳ {{ number_format($tableTotals['deposited']) }}</td>
                    <td class="px-4 py-3 text-right text-amber-700">৳ {{ number_format($tableTotals['pending']) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<style>
@media print {
    aside, header, button { display: none !important; }
    main { margin: 0 !important; padding: 0 !important; }
    .shadow-sm { box-shadow: none !important; }
}
</style>

</x-layouts.admin>
