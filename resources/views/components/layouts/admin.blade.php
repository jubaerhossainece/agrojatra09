<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — Agrojatra09</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex">

{{-- Sidebar --}}
<aside class="w-64 bg-green-800 text-white flex flex-col fixed inset-y-0 left-0 z-50">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-green-700">
        <span class="text-2xl">🌿</span>
        <div>
            <div class="font-bold text-lg leading-tight">Agrojatra09</div>
            <div class="text-green-300 text-xs">একসাথে এগিয়ে চলি</div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('admin.dashboard') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('admin.members.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('admin.members*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Members
        </a>

        <a href="{{ route('admin.deposits.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('admin.deposits*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Deposits
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('admin.reports*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Reports
        </a>

        <a href="{{ route('admin.opinions.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('admin.opinions*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            Opinions
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('admin.users*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Users
        </a>
    </nav>

    {{-- User info at bottom --}}
    <div class="border-t border-green-700 px-4 py-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-green-600 flex items-center justify-center text-sm font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                <div class="text-green-400 text-xs">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left flex items-center gap-2 text-green-200 hover:text-white text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="flex-1 ml-64 flex flex-col min-h-screen">
    {{-- Top bar --}}
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-40">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
        </div>
        <div class="text-sm text-gray-500">
            {{ now()->format('d M Y') }}
        </div>
    </header>

    {{-- Flash messages --}}
    <div class="px-6 pt-4">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Page content --}}
    <main class="flex-1 px-6 pb-8">
        {{ $slot }}
    </main>
</div>

</body>
</html>
