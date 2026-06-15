<x-layouts.admin :title="$label . ' — Payments'">

<div class="space-y-4 max-w-4xl">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.monthly-payments.index', ['year' => $year]) }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Back to {{ $year }} overview</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">{{ $label }}</h2>
        </div>

        @if($payments->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                No payment records generated for this month yet.
                <br>
                <a href="{{ route('admin.monthly-payments.index') }}" class="text-green-600 hover:underline mt-2 inline-block">
                    Go generate them →
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Member</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Expected</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Paid</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Balance</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Due Date</th>
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
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $payment->member->full_name }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">৳ {{ number_format($payment->expected_amount) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-700">৳ {{ number_format($allocated) }}</td>
                            <td class="px-4 py-3 text-right {{ $balance > 0 ? 'text-red-600' : 'text-gray-400' }}">
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
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                {{ $payment->due_date->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td class="px-4 py-2 text-xs font-semibold text-gray-600">Total ({{ $payments->count() }} members)</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-700">৳ {{ number_format($payments->sum('expected_amount')) }}</td>
                        <td class="px-4 py-2 text-right font-bold text-green-700">৳ {{ number_format($payments->sum(fn($p) => $p->allocations->sum('allocated_amount'))) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

</div>

</x-layouts.admin>
