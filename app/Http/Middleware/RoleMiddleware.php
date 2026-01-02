<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Try current default guard first
        $user = Auth::user();

        // If no user found, try all configured guards to find an authenticated user
        if (!$user) {
            foreach (array_keys(config('auth.guards')) as $guard) {
                if (Auth::guard($guard)->check()) {
                    $user = Auth::guard($guard)->user();
                    Auth::shouldUse($guard);
                    break;
                }
            }
        }

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = $user->role;

        // Cek apakah user role ada dalam daftar role yang diizinkan
        if (!in_array($userRole, $roles)) {
            Log::warning('RoleMiddleware: akses ditolak', ['user_id' => $user->id ?? null, 'role' => $userRole, 'required' => $roles]);
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
