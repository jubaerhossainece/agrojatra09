<x-layouts.admin title="Monthly Payments">

<div class="space-y-6">

    {{-- Header + controls --}}
    <div class="flex flex-wrap items-center gap-4">

        {{-- Year switcher --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.monthly-payments.index', ['year' => $year - 1]) }}"
               class="p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="font-semibold text-gray-800 text-lg w-16 text-center">{{ $year }}</span>
            <a href="{{ route('admin.monthly-payments.index', ['year' => $year + 1]) }}"
               class="p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Generate button --}}
        @php
            $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        @endphp
        <form method="POST" action="{{ route('admin.monthly-payments.generate') }}"
              x-data="{ year: {{ now()->year }}, yearOpen: false, month: {{ now()->month }}, monthOpen: false }">
            @csrf
            <input type="hidden" name="year" :value="year">
            <input type="hidden" name="month" :value="month">
            <div class="flex items-center gap-2">

                {{-- Year dropdown --}}
                <div @click.outside="yearOpen = false" class="relative">
                    <button type="button" @click="yearOpen = !yearOpen"
                            class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors cursor-pointer w-24 justify-between">
                        <span x-text="year"></span>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': yearOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="yearOpen"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="absolute z-50 mt-1 w-24 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                        <div class="py-1">
                            @for($y = 2026; $y <= now()->year + 1; $y++)
                                <button type="button" @click="year = {{ $y }}; yearOpen = false"
                                        :class="{ 'bg-green-50 text-green-700 font-semibold': year === {{ $y }} }"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">{{ $y }}</button>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Month dropdown --}}
                <div @click.outside="monthOpen = false" class="relative">
                    <button type="button" @click="monthOpen = !monthOpen"
                            class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors cursor-pointer w-24 justify-between">
                        <span x-text="['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][month - 1]"></span>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': monthOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="monthOpen"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="absolute z-50 mt-1 w-24 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                        <div class="py-1">
                            @foreach($monthNames as $i => $mn)
                                <button type="button" @click="month = {{ $i + 1 }}; monthOpen = false"
                                        :class="{ 'bg-green-50 text-green-700 font-semibold': month === {{ $i + 1 }} }"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">{{ $mn }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    Generate Records
                </button>
            </div>
        </form>
    </div>

    {{-- Matrix grid --}}
    @if($members->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
            No active members found.
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
            <table class="text-xs min-w-max w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 sticky left-0 bg-gray-50 z-10 min-w-36">
                            Member
                        </th>
                        @foreach($months as $m)
                            @php $label = \Carbon\Carbon::create($year, $m, 1)->format('M'); @endphp
                            <th class="px-3 py-3 font-semibold text-gray-500 text-center whitespace-nowrap">
                                <a href="{{ route('admin.monthly-payments.show', [$year, $m]) }}"
                                   class="hover:text-green-700">{{ $label }}</a>
                            </th>
                        @endforeach
                        <th class="px-3 py-3 font-semibold text-gray-600 text-right">Paid</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($members as $member)
                        @php
                            $memberPayments = $member->monthlyPayments->keyBy(fn($p) => $p->payment_month);
                            $paidCount = $memberPayments->filter(fn($p) => $p->total_allocated >= $p->expected_amount)->count();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 font-medium text-gray-800 sticky left-0 bg-white hover:bg-gray-50 z-10">
                                {{ $member->full_name }}
                            </td>
                            @foreach($months as $m)
                                @php $payment = $memberPayments->get($m); @endphp
                                <td class="px-2 py-2 text-center">
                                    @if(!$payment)
                                        <span class="text-gray-300">—</span>
                                    @elseif($payment->total_allocated >= $payment->expected_amount)
                                        @if($payment->is_late)
                                            <span class="inline-block w-5 h-5 rounded-full bg-amber-100 text-amber-700 text-xs leading-5 font-bold" title="Paid Late">L</span>
                                        @else
                                            <span class="inline-block w-5 h-5 rounded-full bg-green-100 text-green-700 text-xs leading-5 font-bold" title="Paid on time">✓</span>
                                        @endif
                                    @elseif($payment->total_allocated > 0)
                                        <span class="inline-block w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs leading-5 font-bold" title="Partial">P</span>
                                    @else
                                        <span class="inline-block w-5 h-5 rounded-full bg-red-100 text-red-600 text-xs leading-5 font-bold" title="Unpaid">✗</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-3 py-2 text-right font-semibold text-gray-700">
                                {{ $paidCount }}/{{ count($months) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1.5"><span class="inline-block w-4 h-4 rounded-full bg-green-100 text-green-700 text-xs leading-4 font-bold text-center">✓</span> Paid on time</span>
            <span class="flex items-center gap-1.5"><span class="inline-block w-4 h-4 rounded-full bg-amber-100 text-amber-700 text-xs leading-4 font-bold text-center">L</span> Paid late</span>
            <span class="flex items-center gap-1.5"><span class="inline-block w-4 h-4 rounded-full bg-blue-100 text-blue-700 text-xs leading-4 font-bold text-center">P</span> Partial</span>
            <span class="flex items-center gap-1.5"><span class="inline-block w-4 h-4 rounded-full bg-red-100 text-red-600 text-xs leading-4 font-bold text-center">✗</span> Unpaid</span>
            <span class="flex items-center gap-1.5"><span class="text-gray-300 font-bold">—</span> Not generated</span>
        </div>
    @endif

</div>

</x-layouts.admin>
