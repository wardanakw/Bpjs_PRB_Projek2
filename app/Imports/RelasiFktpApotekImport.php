<?php

namespace App\Imports;

use App\Models\RelasiFktpApotek;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class RelasiFktpApotekImport implements OnEachRow, WithChunkReading
{
    public function onRow(Row $row)
    {
        $r = $row->toArray();

        static $rowCount = 0;
        $rowCount++;
        if ($rowCount == 1) return;

        if (empty($r[0])) return;

        RelasiFktpApotek::create([
            'kode_fktp'   => $r[0] ?? '',
            'nama_fktp'   => $r[1] ?? '',
            'nama_apotek' => $r[3] ?? '',
            'kode_apotek' => $r[2] ?? '',
        ]);
    }

    public function chunkSize(): int
    {
        return 500; 
    }
}
