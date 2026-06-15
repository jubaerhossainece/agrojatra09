<x-layouts.member title="{{ $member->full_name }}">

<div class="max-w-3xl space-y-6">

    <a href="{{ route('member.members.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Members</a>

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start gap-5">
            @if($member->photo)
                <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->full_name }}"
                     class="w-24 h-24 rounded-xl object-cover flex-shrink-0">
            @else
                <div class="w-24 h-24 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-4xl font-bold flex-shrink-0">
                    {{ strtoupper(substr($member->full_name, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $member->full_name }}</h1>
                <p class="text-gray-500">{{ $member->profession }}
                    @if($member->company_name) · {{ $member->company_name }}@endif
                </p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <x-badge :status="$member->status" />
                    @if($member->blood_group)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            🩸 {{ $member->blood_group }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Email</p>
                <p class="text-gray-800">{{ $member->email }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Phone</p>
                <p class="text-gray-800">{{ $member->phone }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Permanent Address</p>
                <p class="text-gray-700">{{ $member->permanent_address }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Present Address</p>
                <p class="text-gray-700">{{ $member->present_address }}</p>
            </div>
            @if($member->company_address)
                <div class="sm:col-span-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Company Address</p>
                    <p class="text-gray-700">{{ $member->company_address }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Share / Financial Info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Share Investment</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div class="bg-green-50 rounded-lg p-3">
                <p class="text-2xl font-bold text-green-700">{{ $member->total_shares }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Shares Held</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xl font-bold text-gray-700">৳ {{ number_format($member->total_amount) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Commitment</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-xl font-bold text-blue-700">৳ {{ number_format($member->total_deposited) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Deposited</p>
            </div>
            <div class="bg-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-50 rounded-lg p-3">
                <p class="text-xl font-bold text-{{ $member->balance_due > 0 ? 'amber' : 'green' }}-700">
                    ৳ {{ number_format($member->balance_due) }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">Balance Due</p>
            </div>
        </div>
        <div class="mt-3 text-center">
            <x-badge :status="$member->payment_status" />
        </div>
    </div>

    {{-- Nominee --}}
    @if($member->nominee)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="font-semibold text-gray-800 mb-4">Nominee</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Name</p>
                    <p class="font-semibold text-gray-900">{{ $member->nominee->nominee_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Relationship</p>
                    <p class="text-gray-700">{{ $member->nominee->relationship }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Mobile</p>
                    <p class="text-gray-700">{{ $member->nominee->mobile }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Address</p>
                    <p class="text-gray-700">{{ $member->nominee->address }}</p>
                </div>
            </div>
        </div>
    @endif

</div>

</x-layouts.member>
