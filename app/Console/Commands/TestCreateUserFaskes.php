<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Faskes;

class TestCreateUserFaskes extends Command
{
    protected $signature = 'test:create-user';
    protected $description = 'Test creating user and faskes via controller logic';

    public function handle()
    {
        $this->line('=== TEST CREATE USER & FASKES ===');

        // Simulasi data dari form
        $data = [
            'name' => 'Test FKTP User',
            'username' => 'test_fktp_' . time(),
            'password' => 'password123',
            'role' => 'fktp',
            'nama_faskes' => 'Test Puskesmas ABC',
            'jenis_faskes' => 'Puskesmas',
            'alamat_faskes' => 'Jl. Test No. 1',
            'kecamatan' => 'Test Kecamatan',
            'kabupaten' => 'Test Kabupaten',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '40000',
            'kode_faskes' => 'TEST001',
        ];

        $this->line("\nSimulasi create user & faskes:");
        $this->line("  Username: {$data['username']}");
        $this->line("  Role: {$data['role']}");
        $this->line("  Nama Faskes: {$data['nama_faskes']}");

        try {
            // CREATE USER
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $data['role'],
                'fktp_kode' => $data['role'] === 'fktp' ? $data['kode_faskes'] : null,
                'kode_apotek' => null,
            ]);

            $this->line("\n✅ User berhasil dibuat:");
            $this->line("   ID: {$user->id_user}");
            $this->line("   Username: {$user->username}");
            $this->line("   Role: {$user->role}");

            // CREATE FASKES
            $faskes = Faskes::create([
                'kode_faskes' => $data['kode_faskes'],
                'nama_faskes' => $data['nama_faskes'],
                'jenis_faskes' => $data['jenis_faskes'],
                'alamat_faskes' => $data['alamat_faskes'],
                'kecamatan' => $data['kecamatan'],
                'kabupaten' => $data['kabupaten'],
                'provinsi' => $data['provinsi'],
                'kode_pos' => $data['kode_pos'],
                'user_id' => $user->id_user
            ]);

            $this->line("\n✅ Faskes berhasil dibuat:");
            $this->line("   ID: {$faskes->id}");
            $this->line("   Nama: {$faskes->nama_faskes}");
            $this->line("   User ID: {$faskes->user_id}");

            // Verify relationship
            $user_check = User::findOrFail($user->id_user);
            $faskes_check = $user_check->faskes();
            $this->line("\n✅ Relationship check:");
            $this->line("   User memiliki faskes: " . ($user_check->faskes ? 'YES' : 'NO'));

            // Cleanup (optional - comment out jika ingin save data untuk testing)
            $this->line("\nCleaning up test data...");
            // $user->delete();
            // $faskes->delete();
            $this->line("✅ Test berhasil!");

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }

        $this->line("\n=== END TEST ===");
    }
}
