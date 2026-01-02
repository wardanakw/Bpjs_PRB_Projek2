<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function refreshCaptcha()
    {
        return response()->json([
            'captcha' => Captcha::img(),
        ]);
    }
                            
   public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
        'captcha'  => 'required|captcha',
    ], [
        'captcha.captcha' => 'Kode captcha salah. Silakan coba lagi.',
    ]);

    $user = User::where('username', $request->username)->first();
    

    if (!$user || !Hash::check($request->password, $user->password)) {
        return back()->withErrors(['login_error' => 'Username atau password salah.']);
    }

    $guard = $user->role;
    Auth::guard($guard)->login($user);

    $request->session()->regenerate();

    Auth::setDefaultDriver($guard);
    Log::info('AuthController@login', [
        'user_id' => $user->{$user->getKeyName()} ?? null,
        'username' => $user->username ?? null,
        'role' => $user->role ?? null,
        'guard_used' => $guard,
        'guard_check' => Auth::guard($guard)->check(),
    ]);

    $routeName = null;
    switch ($guard) {
        case 'admin':
            $routeName = 'admin.dashboard';
            break;
        case 'rumah_sakit':
            $routeName = 'dashboard.index';
            break;
        case 'fktp':
            $routeName = 'fktp.dashboard';
            break;
        case 'apotek':
            $routeName = 'apotek.dashboard';
            break;
        default:
            Auth::guard($guard)->logout();
            return redirect()->route('login')->withErrors(['login_error' => 'Role tidak dikenali.']);
    }

    return redirect()->route($routeName)->with('status', 'Login berhasil.');
}

   public function logout(Request $request)
{
    $guard = null;

    if (Auth::guard('admin')->check()) {
        $guard = 'admin';
    } elseif (Auth::guard('rumah_sakit')->check()) {
        $guard = 'rumah_sakit';
    } elseif (Auth::guard('fktp')->check()) {
        $guard = 'fktp';
    } elseif (Auth::guard('apotek')->check()) {
        $guard = 'apotek';
    }

    if ($guard) {
        Auth::guard($guard)->logout();
    } else {
        Auth::logout(); 
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('status', 'Anda telah logout.');
}


}
