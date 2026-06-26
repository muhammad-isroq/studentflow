<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah user sudah login
        if (Auth::check()) {
            // Update last_activity secara langsung tanpa memicu event model Eloquent
            Auth::user()->updateQuietly([
                'last_activity' => now(),
            ]);
        }

        return $next($request);
    }
}