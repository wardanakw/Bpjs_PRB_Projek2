<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('obat_prb', function (Blueprint $table) {
            // Tambahkan kolom baru jika belum ada
            if (!Schema::hasColumn('obat_prb', 'satuan')) {
                $table->string('satuan')->nullable()->after('jumlah_obat');
            }
            if (!Schema::hasColumn('obat_prb', 'dosis_obat')) {
                $table->string('dosis_obat')->nullable()->after('satuan');
            }
            if (!Schema::hasColumn('obat_prb', 'aturan_pakai')) {
                $table->string('aturan_pakai')->nullable()->after('dosis_obat');
            }
            

            // Pastikan jumlah_obat unsigned (tidak negatif)
            $table->unsignedInteger('jumlah_obat')->change();

            // Tambahkan index agar query cepat
            $table->index('id_diagnosa');

            // Update foreign key (hapus dulu biar aman)
            $table->dropForeign(['id_diagnosa']);
            $table->foreign('id_diagnosa')
                  ->references('id_diagnosa')
                  ->on('diagnosa_prb')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

   public function down(): void
{
    Schema::table('obat_prb', function (Blueprint $table) {
        // Drop foreign key dulu baru kolom
        if (Schema::hasColumn('obat_prb', 'id_diagnosa')) {
            try {
                $table->dropForeign(['id_diagnosa']);
            } catch (\Exception $e) {
                // Jika foreign key sudah hilang, lanjut saja
            }
        }

        // Drop kolom hanya jika ada
        if (Schema::hasColumn('obat_prb', 'satuan')) {
            $table->dropColumn('satuan');
        }
        if (Schema::hasColumn('obat_prb', 'dosis_obat')) {
            $table->dropColumn('dosis_obat');
        }
        if (Schema::hasColumn('obat_prb', 'aturan_pakai')) {
            $table->dropColumn('aturan_pakai');
        }
        if (Schema::hasColumn('obat_prb', 'catatan')) {
            $table->dropColumn('catatan');
        }

        // ⚠️ Jangan drop index `id_diagnosa` karena itu bawaan dari foreign key
        // Jadi hapus baris $table->dropIndex(['id_diagnosa']); dari kode kamu.
    });
}


};
