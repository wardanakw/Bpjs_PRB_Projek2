<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MultiAuth
{
    public function handle($request, Closure $next, ...$guards)
    {
        // Jika sudah login di salah satu guard, gunakan guard itu
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);
                return $next($request);
            }
        }

        // Jika sudah login tapi guard belum ditentukan (misal di tab baru)
        foreach ($guards as $guard) {
            $user = Auth::guard($guard)->user();
            if ($user) {
                Auth::shouldUse($guard);
                return $next($request);
            }
        }

        // Kalau benar-benar tidak login di guard manapun
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }
}
