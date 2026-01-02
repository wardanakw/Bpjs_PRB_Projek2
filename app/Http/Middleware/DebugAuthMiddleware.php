<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DebugAuthMiddleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        Log::debug('DebugAuthMiddleware', [
            'path' => $request->path(),
            'guards' => $guards,
            'admin_check' => Auth::guard('admin')->check(),
            'rs_check' => Auth::guard('rumah_sakit')->check(),
            'fktp_check' => Auth::guard('fktp')->check(),
            'apotek_check' => Auth::guard('apotek')->check(),
            'default_user' => Auth::user()?->username,
        ]);

        return $next($request);
    }
}
