@props(['label', 'value', 'sub' => null, 'color' => 'green', 'icon' => null])

@php
$colors = [
    'green'  => 'bg-green-50 border-green-200 text-green-700',
    'amber'  => 'bg-amber-50 border-amber-200 text-amber-700',
    'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
    'red'    => 'bg-red-50 border-red-200 text-red-700',
    'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
];
$cls = $colors[$color] ?? $colors['green'];
@endphp

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $value }}</p>
            @if($sub)
                <p class="mt-1 text-sm text-gray-500">{{ $sub }}</p>
            @endif
        </div>
        @if($icon)
            <div class="p-3 rounded-full {{ $cls }}">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
