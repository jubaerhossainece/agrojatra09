<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrojatra09 — একসাথে এগিয়ে চলি</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

{{-- Navbar --}}
<nav class="bg-green-800 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🌿</span>
            <div>
                <span class="font-bold text-xl">Agrojatra09</span>
                <span class="hidden sm:inline text-green-300 text-xs ml-2">Batch 2009</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="bg-amber-400 hover:bg-amber-500 text-gray-900 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Admin Panel →
                    </a>
                @else
                    <a href="{{ route('member.dashboard') }}"
                       class="bg-amber-400 hover:bg-amber-500 text-gray-900 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        My Dashboard →
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="border border-white/40 hover:bg-white/10 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Sign In
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- Hero Section --}}
<section class="bg-gradient-to-br from-green-800 via-green-700 to-green-600 text-white py-24 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <div class="text-6xl mb-6">🌿</div>
        <h1 class="text-5xl sm:text-6xl font-extrabold mb-4 leading-tight">
            Agrojatra<span class="text-amber-400">09</span>
        </h1>
        <p class="text-2xl text-green-100 mb-3 font-light">একসাথে এগিয়ে চলি</p>
        <p class="text-green-200 text-lg mb-10 max-w-2xl mx-auto">
            A member investment group of SSC Batch 2009 from Magura, Bangladesh. Building our future together, one share at a time.
        </p>

        {{-- Stats Band --}}
        <div class="flex flex-wrap justify-center gap-8 mb-10">
            @php
                $totalMembers = \App\Models\Member::count();
                $totalShares = \App\Models\Share::sum('number_of_shares');
                $totalShareAmount = \App\Models\Share::sum('total_amount');
                $totalDeposited = \App\Models\Deposit::sum('amount');
            @endphp
            <div class="text-center">
                <div class="text-4xl font-bold text-white">{{ $totalMembers }}</div>
                <div class="text-green-300 text-sm mt-1">Members</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-amber-400">{{ $totalShares }}</div>
                <div class="text-green-300 text-sm mt-1">Total Shares</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-white">৳ {{ number_format($totalShareAmount) }}</div>
                <div class="text-green-300 text-sm mt-1">Committed</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-amber-400">৳ {{ number_format($totalDeposited) }}</div>
                <div class="text-green-300 text-sm mt-1">Collected</div>
            </div>
        </div>

        @guest
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-500 text-gray-900 font-bold px-8 py-4 rounded-xl text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                Member Login →
            </a>
        @endguest
    </div>
</section>

{{-- Features --}}
<section class="py-20 px-4">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-3">About Agrojatra09</h2>
        <p class="text-center text-gray-500 mb-12 max-w-2xl mx-auto">
            We are a collective of friends from SSC Batch 2009, Magura — united by shared memories and a vision for collective growth.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-7 text-center hover:shadow-md transition-shadow">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Share Investment</h3>
                <p class="text-gray-500 text-sm">Each member holds shares at BDT 2,000 per share. Track your investment transparently.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-7 text-center hover:shadow-md transition-shadow">
                <div class="text-4xl mb-4">🏦</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Bank Deposits</h3>
                <p class="text-gray-500 text-sm">All deposits are recorded with bank reference, receipt numbers, and dates for full accountability.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-7 text-center hover:shadow-md transition-shadow">
                <div class="text-4xl mb-4">🌐</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Member Portal</h3>
                <p class="text-gray-500 text-sm">Every member can log in to view their profile, shares, deposit history, and balance in real time.</p>
            </div>
        </div>
    </div>
</section>

{{-- Share Value --}}
<section class="bg-green-700 text-white py-16 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-4">1 Share = BDT 2,000</h2>
        <p class="text-green-200 text-lg mb-8 max-w-xl mx-auto">
            Our group has committed to {{ $totalShares }} shares — a total of BDT {{ number_format($totalShareAmount) }} in investment capital, building something meaningful together.
        </p>
        @guest
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-white text-green-800 font-bold px-6 py-3 rounded-xl text-base transition-all hover:shadow-lg">
                Access Member Portal →
            </a>
        @endguest
    </div>
</section>

{{-- Footer --}}
<footer class="bg-gray-900 text-gray-400 py-10 px-4 text-center">
    <div class="flex justify-center items-center gap-2 text-xl font-bold text-white mb-2">
        <span>🌿</span>
        <span>Agrojatra09</span>
    </div>
    <p class="text-sm text-gray-500">একসাথে এগিয়ে চলি · SSC Batch 2009 · Magura, Bangladesh</p>
    <p class="text-xs text-gray-600 mt-3">© {{ date('Y') }} Agrojatra09. All rights reserved.</p>
</footer>

</body>
</html>
