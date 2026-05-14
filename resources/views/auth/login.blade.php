<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Agrojatra09</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center bg-gradient-to-br from-green-800 to-green-600 px-4 min-h-screen">

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-2xl p-8">

        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🌿</div>
            <h1 class="text-2xl font-extrabold text-gray-900">Agrojatra09</h1>
            <p class="text-green-600 text-sm mt-1">একসাথে এগিয়ে চলি</p>
        </div>

        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}"
                       placeholder="your@email.com">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}"
                       placeholder="••••••••">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="text-sm text-gray-600">Remember me</span>
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit"
                    class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-sm">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                ← Back to Home
            </a>
        </div>
    </div>

    <p class="text-center text-green-200 text-xs mt-4">
        SSC Batch 2009 · Magura, Bangladesh
    </p>
</div>

</body>
</html>
