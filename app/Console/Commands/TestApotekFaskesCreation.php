<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Faskes;

class TestApotekFaskesCreation extends Command
{
    protected $signature = 'test:apotek-faskes';
    protected $description = 'Test creating apotek faskes (no kode_faskes required)';

    public function handle()
    {
        $this->line('=== TEST APOTEK FASKES CREATION ===');

        $data = [
            'name' => 'Test Apotek User',
            'username' => 'test_apotek_' . time(),
            'password' => 'password123',
            'role' => 'apotek',
            'nama_faskes' => 'Apotek Test 123',
            'jenis_faskes' => 'Apotek',
            'alamat_faskes' => 'Jl. Test Apotek No. 1',
            'kecamatan' => 'Test Kecamatan',
            'kabupaten' => 'Test Kabupaten',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '40000',
            'kode_faskes' => null, // NULL untuk apotek (tidak dipakai)
            'kode_apotek' => '0120A060', // Apotek code yang ada di database
        ];

        $this->line("\nSimulasi create apotek faskes:");
        $this->line("  Username: {$data['username']}");
        $this->line("  Role: {$data['role']}");
        $this->line("  Nama Faskes: {$data['nama_faskes']}");
        $this->line("  Kode Apotek: {$data['kode_apotek']}");

        try {
            // CREATE USER
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $data['role'],
                'fktp_kode' => null,
                'kode_apotek' => $data['kode_apotek'],
            ]);

            $this->line("\n User berhasil dibuat:");
            $this->line("   ID: {$user->id_user}");
            $this->line("   Username: {$user->username}");
            $this->line("   Role: {$user->role}");
            $this->line("   Kode Apotek: {$user->kode_apotek}");

            // CREATE FASKES (kode_faskes = NULL)
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

            $this->line("\n Faskes berhasil dibuat:");
            $this->line("   ID: {$faskes->id}");
            $this->line("   Nama: {$faskes->nama_faskes}");
            $this->line("   Kode Faskes: " . ($faskes->kode_faskes ?? 'NULL'));
            $this->line("   User ID: {$faskes->user_id}");

            $this->line("\n Test berhasil! Apotek faskes dapat dibuat tanpa kode_faskes");

        } catch (\Exception $e) {
            $this->error(" Error: " . $e->getMessage());
        }

        $this->line("\n=== END TEST ===");
    }
}
