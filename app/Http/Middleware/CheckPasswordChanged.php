<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Ambil user yang sedang login
        $user = $request->user();

        // Cek jika user yang login memiliki role 'guru'
        // dan sedang mencoba mengakses dashboard utama
        if ($user && $user->hasRole('guru') && $request->routeIs('filament.admin.pages.dashboard')) {
            // Arahkan mereka ke halaman jadwal khusus guru
            return redirect()->route('filament.admin.pages.teacher-schedule');
        }

        // Untuk user lain atau halaman lain, lanjutkan seperti biasa
        return $next($request);
    }
}