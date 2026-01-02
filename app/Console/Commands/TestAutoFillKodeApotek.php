<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RelasiFktpApotek;

class TestAutoFillKodeApotek extends Command
{
    protected $signature = 'test:auto-fill-kode-apotek';
    protected $description = 'Test auto-fill kode_apotek logic when patient is created with fktp_asal';

    public function handle()
    {
        $this->line('=== TEST AUTO-FILL KODE_APOTEK ===');

        // Simulasi input dari user
        $fktp_asal = 'BANJARAN KOTA'; // Salah satu nama FKTP dari mapping
        $this->line("\nSimulasi input patient dengan fktp_asal: '{$fktp_asal}'");

        // Logic dari PasienController::store
        $kodeFktp = null; // Assume fktp_kode tidak dikirim
        $kodeApotek = null; // Assume kode_apotek tidak dikirim

        if (empty($kodeFktp) && !empty($fktp_asal)) {
            $relasi = RelasiFktpApotek::where('nama_fktp', $fktp_asal)->first();
            if ($relasi) {
                $kodeFktp = $relasi->kode_fktp;
                if (empty($kodeApotek)) {
                    $kodeApotek = $relasi->kode_apotek;
                }
                $this->line("✅ Mapping ditemukan:");
                $this->line("   fktp_asal: {$relasi->nama_fktp}");
                $this->line("   kode_fktp (auto-filled): {$kodeFktp}");
                $this->line("   kode_apotek (auto-filled): {$kodeApotek}");
            } else {
                $this->error("❌ Mapping tidak ditemukan untuk '{$fktp_asal}'");
            }
        }

        // Test dengan berbagai FKTP
        $this->line("\n\nTest dengan semua FKTP yang tersedia:");
        $all_fktp = RelasiFktpApotek::selectRaw('DISTINCT nama_fktp, kode_fktp, kode_apotek')
                                    ->orderBy('nama_fktp')
                                    ->limit(5)
                                    ->get();
        
        foreach ($all_fktp as $fktp) {
            $this->line("  - {$fktp->nama_fktp} → kode_fktp: {$fktp->kode_fktp}, kode_apotek: {$fktp->kode_apotek}");
        }

        $this->line("\n=== END TEST ===");
    }
}
