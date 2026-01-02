<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('diagnosa_prb', function (Blueprint $table) {
            // Tambah kolom untuk bukti bayar PDF dan asal klaim
            if (!Schema::hasColumn('diagnosa_prb', 'bukti_bayar_pdf')) {
                $table->string('bukti_bayar_pdf')->nullable()->after('file_upload');
            }
            if (!Schema::hasColumn('diagnosa_prb', 'asal_klaim')) {
                $table->enum('asal_klaim', ['rumah_sakit', 'apotek'])->nullable()->after('bukti_bayar_pdf');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosa_prb', function (Blueprint $table) {
            if (Schema::hasColumn('diagnosa_prb', 'bukti_bayar_pdf')) {
                $table->dropColumn('bukti_bayar_pdf');
            }
            if (Schema::hasColumn('diagnosa_prb', 'asal_klaim')) {
                $table->dropColumn('asal_klaim');
            }
        });
    }
};
