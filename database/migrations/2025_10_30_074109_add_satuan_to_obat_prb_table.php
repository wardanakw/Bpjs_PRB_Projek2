<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perubahan tabel.
     */
    public function up(): void
    {
        Schema::table('obat_prb', function (Blueprint $table) {
            // Cek dulu apakah kolom 'satuan' sudah ada
            if (!Schema::hasColumn('obat_prb', 'satuan')) {
                $table->string('satuan', 50)->nullable()->after('jumlah_obat');
            }
        });
    }

    /**
     * Kembalikan perubahan (rollback).
     */
    public function down(): void
    {
        Schema::table('obat_prb', function (Blueprint $table) {
            if (Schema::hasColumn('obat_prb', 'satuan')) {
                $table->dropColumn('satuan');
            }
        });
    }
};
