<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (!auth()->user()->isMember()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
