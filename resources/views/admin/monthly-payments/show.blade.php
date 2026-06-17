<x-layouts.admin :title="$label . ' — Payments'">

<div class="space-y-4 max-w-4xl">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.monthly-payments.index', ['year' => $year]) }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Back to {{ $year }} overview</a>

        <div class="flex items-center gap-2" x-data="{ deletePending: false }">

            {{-- Edit Due Date --}}
            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                <button @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Edit Due Date
                </button>
                <div x-show="open"
                     style="display:none"
                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 z-50 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg p-4">
                    <p class="text-xs text-gray-500 mb-2">Set a new due date for all <strong>{{ $label }}</strong> records.</p>
                    <form method="POST" action="{{ route('admin.monthly-payments.update-due-date', [$year, $month]) }}">
                        @csrf
                        @method('PATCH')
                        <input type="date" name="due_date"
                               value="{{ $payments->first()?->due_date?->format('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 mb-3">
                        <button type="submit"
                                class="w-full bg-green-700 hover:bg-green-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Update Due Date
                        </button>
                    </form>
                </div>
            </div>

            {{-- Delete Month --}}
            <div x-data="{ confirm: false }">
                <button x-show="!confirm" @click="confirm = true"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm border border-red-200 rounded-lg bg-white hover:bg-red-50 text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Month
                </button>
                <div x-show="confirm" style="display:none" class="flex items-center gap-2">
                    <span class="text-xs text-red-600 font-medium">Delete all records?</span>
                    <form method="POST" action="{{ route('admin.monthly-payments.delete-month', [$year, $month]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            Yes, delete
                        </button>
                    </form>
                    <button @click="confirm = false"
                            class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

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
                        <th class="px-4 py-3"></th>
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
                            <td class="px-4 py-3 text-center">
                                @if($isPaid && $payment->is_late)
                                    <form method="POST" action="{{ route('admin.monthly-payments.override-late', $payment->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs text-amber-700 hover:text-amber-900 underline whitespace-nowrap"
                                                title="Override: mark as paid on time">
                                            Mark On-Time
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td class="px-4 py-2 text-xs font-semibold text-gray-600">Total ({{ $payments->count() }} members)</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-700">৳ {{ number_format($payments->sum('expected_amount')) }}</td>
                        <td class="px-4 py-2 text-right font-bold text-green-700">৳ {{ number_format($payments->sum(fn($p) => $p->allocations->sum('allocated_amount'))) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    {{-- Surplus deposit note --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 text-sm text-blue-800">
        <p class="font-semibold mb-1">How surplus deposits work</p>
        <p>If a member deposits more than their monthly amount, the surplus automatically flows to the <strong>next unpaid month</strong>. Example: if a member owes ৳2,000/month and deposits ৳10,000, it covers 5 months at once. Deleting a month's records reallocates all existing deposits to the remaining months.</p>
    </div>

</div>

</x-layouts.admin>
