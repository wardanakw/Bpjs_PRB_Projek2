<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RelasiFktpApotekImport;

class RelasiFktpApotekSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('app/public/Mapping.xlsx');

        if (!file_exists($filePath)) {
            echo "❌ File Excel tidak ditemukan! → $filePath\n";
            return;
        }

        // Bersihkan tabel sebelum import
        DB::table('relasi_fktp_apotek')->truncate();

        // Import menggunakan CHUNK → anti memory limit
        Excel::import(new RelasiFktpApotekImport, $filePath);

        echo "✔ Import sukses!\n";
    }
}
