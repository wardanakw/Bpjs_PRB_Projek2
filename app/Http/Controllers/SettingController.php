<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function index()
    {
        return view('setting.index');
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 401);
            }

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $user->name = $request->name;
            $user->save();

            // Refresh the authenticated user session
            Auth::login($user);

            return response()->json(['success' => true, 'message' => 'Name berhasil diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 401);
            }

            $request->validate([
                'old_password' => 'required',
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            ]);

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 422);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['success' => true, 'message' => 'Password berhasil diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
