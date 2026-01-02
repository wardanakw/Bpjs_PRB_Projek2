<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureApotekGuard
{
    public function handle($request, Closure $next)
    {
        // Jika apotek guard sudah terverifikasi, lanjutkan
        if (Auth::guard('apotek')->check()) {
            return $next($request);
        }

        // Jika ada user di default guard tapi dia bukan di apotek guard, logout
        if (Auth::check() && Auth::user()->role !== 'apotek') {
            Auth::logout();
        }

        // Jika tidak ada apotek user, redirect ke login
        if (!Auth::guard('apotek')->check()) {
            Log::warning('EnsureApotekGuard: Apotek user not authenticated', [
                'path' => $request->path(),
            ]);
            return redirect()->route('login');
        }

        return $next($request);
    }
}
