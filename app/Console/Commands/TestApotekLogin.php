<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestApotekLogin extends Command
{
    protected $signature = 'test:apotek-login';
    protected $description = 'Test apotek user login and auth check';

    public function handle()
    {
        $this->line('=== TEST APOTEK LOGIN & GUARD ===');

        // Cari user apotek
        $apotek_user = User::where('role', 'apotek')->first();
        
        if (!$apotek_user) {
            $this->error("❌ User apotek tidak ditemukan");
            return;
        }

        $this->line("\n1. User Apotek ditemukan:");
        $this->line("   ID: {$apotek_user->id_user}");
        $this->line("   Username: {$apotek_user->username}");
        $this->line("   Role: {$apotek_user->role}");
        $this->line("   Kode Apotek: {$apotek_user->kode_apotek}");

        // Simulasi login
        $this->line("\n2. Simulasi login dengan guard 'apotek':");
        Auth::guard('apotek')->login($apotek_user);
        
        if (Auth::guard('apotek')->check()) {
            $this->line("   ✅ User berhasil login dengan guard 'apotek'");
            $auth_user = Auth::guard('apotek')->user();
            $this->line("   - Username: {$auth_user->username}");
            $this->line("   - Role: {$auth_user->role}");
        } else {
            $this->error("   ❌ User gagal login dengan guard 'apotek'");
        }

        // Cek default guard
        $this->line("\n3. Cek default guard (Auth::user()):");
        $default_user = Auth::user();
        if ($default_user) {
            $this->line("   ✅ Default guard ada user: {$default_user->username}");
        } else {
            $this->line("   ❌ Default guard tidak ada user");
        }

        // Test middleware role:apotek
        $this->line("\n4. Cek role middleware:");
        $this->line("   Guard 'apotek' check: " . (Auth::guard('apotek')->check() ? 'YES' : 'NO'));
        $this->line("   User role: " . Auth::guard('apotek')->user()?->role);
        $this->line("   In array ['apotek']: " . (in_array(Auth::guard('apotek')->user()?->role, ['apotek']) ? 'YES' : 'NO'));

        $this->line("\n=== END TEST ===");
    }
}
