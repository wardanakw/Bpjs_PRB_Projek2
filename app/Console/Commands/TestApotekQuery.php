<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RelasiFktpApotek;
use Illuminate\Support\Facades\DB;

class TestApotekQuery extends Command
{
    protected $signature = 'test:apotek-query';
    protected $description = 'Test the exact query that search endpoint uses';

    public function handle()
    {
        $this->line('=== TEST APOTEK QUERY ===');

        // Simulasi query dari controller
        $keyword = 'kimia'; // Atau bisa coba keyword lain
        $kode_apotek = '0120L004'; // Dari user

        $this->line("\n1. Query tanpa role filter (should return results):");
        $query1 = RelasiFktpApotek::where(function ($q) use ($keyword) {
            $q->where('nama_fktp', 'LIKE', "%{$keyword}%")
              ->orWhere('kode_fktp', 'LIKE', "%{$keyword}%");
        })->limit(20);

        $this->line("SQL: " . $query1->toSql());
        $this->line("Bindings: " . json_encode($query1->getBindings()));
        $result1 = $query1->get();
        $this->line("Results count: " . count($result1));
        foreach ($result1 as $row) {
            $this->line("  - {$row->kode_fktp}: {$row->nama_fktp} (apotek: {$row->kode_apotek})");
        }

        $this->line("\n2. Query dengan role filter APOTEK (current logic):");
        $query2 = RelasiFktpApotek::where(function ($q) use ($keyword) {
            $q->where('nama_fktp', 'LIKE', "%{$keyword}%")
              ->orWhere('kode_fktp', 'LIKE', "%{$keyword}%");
        });

        // Apply apotek filter
        $query2->where(function ($q2) use ($kode_apotek) {
            $q2->where('kode_apotek', $kode_apotek)
               ->orWhereRaw("TRIM(LEADING '0' FROM kode_apotek) = TRIM(LEADING '0' FROM ?)", [$kode_apotek]);
        });
        $query2->limit(20);

        $this->line("SQL: " . $query2->toSql());
        $this->line("Bindings: " . json_encode($query2->getBindings()));
        
        // Try to execute, catch error
        try {
            $result2 = $query2->get();
            $this->line("Results count: " . count($result2));
            foreach ($result2 as $row) {
                $this->line("  - {$row->kode_fktp}: {$row->nama_fktp} (apotek: {$row->kode_apotek})");
            }
        } catch (\Exception $e) {
            $this->error("ERROR: " . $e->getMessage());
        }

        $this->line("\n3. Test TRIM function support:");
        try {
            $raw_result = DB::select("SELECT COUNT(*) as cnt FROM relasi_fktp_apotek WHERE TRIM(LEADING '0' FROM kode_apotek) = TRIM(LEADING '0' FROM '0120L004')");
            $this->line("TRIM() works! Count: " . $raw_result[0]->cnt);
        } catch (\Exception $e) {
            $this->error("TRIM() ERROR: " . $e->getMessage());
            $this->line("\nTrying alternative without TRIM:");
            $alt = RelasiFktpApotek::where('kode_apotek', $kode_apotek)->count();
            $this->line("Exact match: {$alt}");
        }

        $this->line("\n=== END TEST ===");
    }
}
