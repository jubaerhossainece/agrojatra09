@props(['status'])

@php
$map = [
    'paid'     => 'bg-green-100 text-green-800',
    'partial'  => 'bg-amber-100 text-amber-800',
    'pending'  => 'bg-red-100 text-red-800',
    'active'   => 'bg-green-100 text-green-800',
    'inactive' => 'bg-gray-100 text-gray-600',
    'admin'    => 'bg-purple-100 text-purple-800',
    'member'   => 'bg-blue-100 text-blue-800',
];
$cls = $map[$status] ?? 'bg-gray-100 text-gray-600';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cls }}">
    {{ ucfirst($status) }}
</span>
