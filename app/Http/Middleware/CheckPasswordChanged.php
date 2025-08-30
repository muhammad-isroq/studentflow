<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Filament\Pages\ChangePassword;


class CheckPasswordChanged
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Jika user sudah login, passwordnya belum pernah diubah,
        // dan user TIDAK sedang di halaman ganti password
        if (
            $user &&
            $user->hasRole('staff') &&
            empty($user->password_changed_at) &&
            !$request->routeIs('filament.admin.pages.change-password')
        ) {
            return redirect()->route('filament.admin.pages.change-password');
        }

        return $next($request);
    }
}

