<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MultiGuardAuth
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = $guards ?: ['admin', 'rumah_sakit', 'fktp', 'apotek', 'web'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);
                return $next($request);
            }
        }

        return redirect()->route('login');
    }
}
