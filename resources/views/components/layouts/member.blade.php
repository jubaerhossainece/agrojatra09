<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'My Portal' }} — Agrojatra09</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">

<nav class="bg-green-800 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🌿</span>
                <div>
                    <span class="font-bold text-lg">Agrojatra09</span>
                    <span class="hidden sm:inline text-green-300 text-xs ml-2">একসাথে এগিয়ে চলি</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('member.dashboard') }}"
                   class="text-sm px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('member.dashboard') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    Dashboard
                </a>
                <a href="{{ route('member.profile') }}"
                   class="text-sm px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('member.profile') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    Profile
                </a>
                <a href="{{ route('member.deposits.index') }}"
                   class="text-sm px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('member.deposits*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    Deposits
                </a>
                <a href="{{ route('member.payment-schedule') }}"
                   class="text-sm px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('member.payment-schedule') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    Payments
                </a>
                {{-- Members nav link hidden until group agrees to enable transparency --}}
                {{-- <a href="{{ route('member.members.index') }}"
                   class="text-sm px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('member.members*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    Members
                </a> --}}

                <div class="flex items-center gap-3 pl-3 border-l border-green-700">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-green-200 hover:text-white text-sm transition-colors">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    {{ $slot }}
</main>

</body>
</html>
