<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RelasiFktpApotek;

class TestApotekQueryFixed extends Command
{
    protected $signature = 'test:apotek-query-fixed';
    protected $description = 'Test the fixed query (apotek shows all FKTP without keyword filter)';

    public function handle()
    {
        $this->line('=== TEST FIXED APOTEK QUERY ===');

        $kode_apotek = '0120L004'; // User's kode_apotek

        $this->line("\nQuery untuk role APOTEK (tanpa keyword filter):");
        $query = RelasiFktpApotek::where(function ($q) use ($kode_apotek) {
            $q->where('kode_apotek', $kode_apotek)
              ->orWhereRaw("TRIM(LEADING '0' FROM kode_apotek) = TRIM(LEADING '0' FROM ?)", [$kode_apotek]);
        })->limit(20);

        $this->line("SQL: " . $query->toSql());
        $this->line("Bindings: " . json_encode($query->getBindings()));
        
        $result = $query->get();
        $this->line("\n✅ Results count: " . count($result));
        
        if (count($result) > 0) {
            $this->line("\nData yang ditampilkan untuk apotek (kode_apotek = {$kode_apotek}):");
            foreach ($result as $row) {
                $this->line("  - {$row->kode_fktp}: {$row->nama_fktp}");
            }
        }

        $this->line("\n=== END TEST ===");
    }
}
