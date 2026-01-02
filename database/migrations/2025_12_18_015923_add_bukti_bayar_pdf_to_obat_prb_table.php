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
        Schema::table('obat_prb', function (Blueprint $table) {
            if (!Schema::hasColumn('obat_prb', 'bukti_bayar_pdf')) {
                $table->string('bukti_bayar_pdf')->nullable()->after('tanggal_klaim');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat_prb', function (Blueprint $table) {
            //
        });
    }
};
