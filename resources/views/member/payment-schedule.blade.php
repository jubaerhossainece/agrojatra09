<x-layouts.member title="Payment Schedule">

<div class="max-w-3xl space-y-6">

    {{-- Summary cards --}}
    @php
        $totalExpected  = $payments->sum('expected_amount');
        $totalPaid      = $payments->sum(fn($p) => $p->allocations->sum('allocated_amount'));
        $totalBalance   = max(0, $totalExpected - $totalPaid);
        $paidMonths     = $payments->filter(fn($p) => $p->allocations->sum('allocated_amount') >= $p->expected_amount)->count();
        $lateMonths     = $payments->filter(fn($p) => $p->is_late)->count();
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-gray-900">{{ $payments->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Months Generated</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-green-700">{{ $paidMonths }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Months Paid</p>
        </div>
        <div class="bg-{{ $lateMonths > 0 ? 'amber' : 'gray' }}-50 rounded-xl border border-{{ $lateMonths > 0 ? 'amber' : 'gray' }}-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-{{ $lateMonths > 0 ? 'amber' : 'gray' }}-700">{{ $lateMonths }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Late Payments</p>
        </div>
        <div class="bg-{{ $totalBalance > 0 ? 'red' : 'gray' }}-50 rounded-xl border border-{{ $totalBalance > 0 ? 'red' : 'gray' }}-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-{{ $totalBalance > 0 ? 'red' : 'gray' }}-700">৳ {{ number_format($totalBalance) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Outstanding</p>
        </div>
    </div>

    {{-- Schedule table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Monthly Payment Schedule</h2>
        </div>

        @if($payments->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-gray-400 text-sm">No payment records yet. The admin generates these each month.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Month</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Expected</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Paid</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Balance</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $payment)
                        @php
                            $allocated = $payment->allocations->sum('allocated_amount');
                            $balance   = max(0, $payment->expected_amount - $allocated);
                            $isPaid    = $allocated >= $payment->expected_amount;
                            $isPartial = !$isPaid && $allocated > 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ \Carbon\Carbon::create($payment->payment_year, $payment->payment_month, 1)->format('F Y') }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600">৳ {{ number_format($payment->expected_amount) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-700">৳ {{ number_format($allocated) }}</td>
                            <td class="px-4 py-3 text-right hidden sm:table-cell {{ $balance > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                {{ $balance > 0 ? '৳ ' . number_format($balance) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($isPaid && $payment->is_late)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Late</span>
                                @elseif($isPaid)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">On Time</span>
                                @elseif($isPartial)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Partial</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs hidden md:table-cell">
                                {{ $payment->due_date->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td class="px-4 py-2 text-xs font-semibold text-gray-600">Total</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-700">৳ {{ number_format($totalExpected) }}</td>
                        <td class="px-4 py-2 text-right font-bold text-green-700">৳ {{ number_format($totalPaid) }}</td>
                        <td colspan="3" class="hidden sm:table-cell"></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <p class="text-xs text-gray-400 text-center">
        Payments are auto-allocated from your deposits to the oldest unpaid month first.
        If you deposit more than the monthly amount, the surplus covers future months automatically.
        Due date is the 20th of each month (30th for June 2026).
    </p>

</div>

</x-layouts.member>
