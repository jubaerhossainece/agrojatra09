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

            {{-- Brand --}}
            <div class="flex items-center gap-3">
                <span class="text-2xl">🌿</span>
                <div>
                    <span class="font-bold text-lg">Agrojatra09</span>
                    <span class="hidden sm:inline text-green-300 text-xs ml-2">একসাথে এগিয়ে চলি</span>
                </div>
            </div>

            {{-- Nav links + user dropdown --}}
            <div class="flex items-center gap-1">
                <a href="{{ route('member.dashboard') }}"
                   class="text-sm px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('member.dashboard') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    Dashboard
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

                {{-- User dropdown --}}
                <div class="relative ml-3 pl-3 border-l border-green-700"
                     x-data="{ open: false }" @click.outside="open = false">

                    <button @click="open = !open"
                            class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-green-700 transition-colors focus:outline-none group">
                        <div class="w-8 h-8 rounded-full bg-green-600 group-hover:bg-green-500 transition-colors flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="text-green-100 text-sm hidden md:block max-w-28 truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-green-300 transition-transform duration-200 hidden md:block"
                             :class="{ 'rotate-180': open }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown panel --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50"
                         style="display: none;">

                        {{-- User info header --}}
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-green-600 font-medium mt-0.5">Member</p>
                        </div>

                        {{-- Links --}}
                        <div class="py-1">
                            <a href="{{ route('member.profile') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors
                                      {{ request()->routeIs('member.profile') ? 'bg-green-50 text-green-700 font-medium' : '' }}">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Profile
                            </a>
                            <a href="{{ route('member.deposits.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                My Deposits
                            </a>
                            <a href="{{ route('member.payment-schedule') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Payment Schedule
                            </a>
                        </div>

                        {{-- Sign out --}}
                        <div class="border-t border-gray-100 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
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

{{-- Global Confirm Modal --}}
<div x-data="{
        show: false,
        title: '',
        message: '',
        confirmLabel: 'Confirm',
        confirmClass: 'bg-red-600 hover:bg-red-700',
        pendingTarget: null,
        open(detail) {
            this.title        = detail.title        || 'Are you sure?';
            this.message      = detail.message      || '';
            this.confirmLabel = detail.confirmLabel || 'Confirm';
            this.confirmClass = detail.confirmClass || 'bg-red-600 hover:bg-red-700';
            this.pendingTarget = detail.target      || null;
            this.show = true;
        },
        confirm() { if (this.pendingTarget) this.pendingTarget.submit(); this.show = false; },
        cancel()  { this.show = false; this.pendingTarget = null; }
     }"
     @open-confirm.window="open($event.detail)"
     @keydown.escape.window="cancel()"
     x-show="show"
     style="display:none"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">

    {{-- Backdrop --}}
    <div @click="cancel()"
         class="absolute inset-0 bg-gray-900/50"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Dialog card --}}
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>

        {{-- Text --}}
        <div class="text-center mb-6">
            <h3 x-text="title" class="text-lg font-semibold text-gray-900"></h3>
            <p x-text="message" class="mt-1.5 text-sm text-gray-500"></p>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-3">
            <button @click="cancel()"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button @click="confirm()"
                    :class="confirmClass"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-white transition-colors"
                    x-text="confirmLabel">
            </button>
        </div>
    </div>
</div>

</body>
</html>
