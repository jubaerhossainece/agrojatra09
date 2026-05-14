<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if (auth()->check() && auth()->user()->isMember()) {
                return redirect()->route('member.dashboard');
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
