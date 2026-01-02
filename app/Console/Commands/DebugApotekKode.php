<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DebugApotekKode extends Command
{
    protected $signature = 'debug:apotek';
    protected $description = 'Debug apotek kode_apotek matching';

    public function handle()
    {
        $this->line('=== DEBUG APOTEK KODE_APOTEK ===');

        // 1. Cek user (id_user 12)
        $this->line("\n1. User record (id_user 12):");
        $user = User::where('id_user', 12)->first();
        if ($user) {
            $this->line("   ID: {$user->id_user}");
            $this->line("   Username: {$user->username}");
            $this->line("   Role: {$user->role}");
            $this->line("   Kode Apotek: " . ($user->kode_apotek ?? 'NULL'));
        } else {
            $this->line("   User NOT FOUND");
        }

        // 2. Data di tabel relasi_fktp_apotek
        $this->line("\n2. Data di tabel relasi_fktp_apotek:");
        $count = DB::table('relasi_fktp_apotek')->count();
        $this->line("   Total records: {$count}");
        
        $apotek_data = DB::table('relasi_fktp_apotek')->limit(5)->get();
        $this->line("   Sample data (first 5):");
        foreach ($apotek_data as $row) {
            $this->line("     - kode_fktp: {$row->kode_fktp}, nama_fktp: {$row->nama_fktp}, kode_apotek: {$row->kode_apotek}");
        }

        // 3. Cari kode_apotek dengan variasi
        $this->line("\n3. Cari kode_apotek dengan variasi:");
        if ($user && !empty($user->kode_apotek)) {
            $search_kode = $user->kode_apotek;
            $this->line("   Searching for: {$search_kode}");
            
            $exact = DB::table('relasi_fktp_apotek')->where('kode_apotek', $search_kode)->count();
            $this->line("   - Exact match: {$exact} records");
            
            $like = DB::table('relasi_fktp_apotek')->where('kode_apotek', 'LIKE', "%{$search_kode}%")->count();
            $this->line("   - LIKE match: {$like} records");
            
            // Cek semua kode_apotek unik
            $unique_codes = DB::table('relasi_fktp_apotek')->selectRaw('DISTINCT kode_apotek')->get();
            $this->line("   - Unique kode_apotek in DB:");
            foreach ($unique_codes as $code) {
                $this->line("     * '{$code->kode_apotek}'");
            }
        } else {
            $this->line("   User kode_apotek is NULL/empty");
        }

        // 4. Migration status
        $this->line("\n4. kode_apotek migration status:");
        $migrations = DB::table('migrations')->whereRaw("migration LIKE '%kode_apotek%'")->get();
        foreach ($migrations as $m) {
            $this->line("   - {$m->migration}");
        }

        $this->line("\n=== END DEBUG ===");
    }
}
