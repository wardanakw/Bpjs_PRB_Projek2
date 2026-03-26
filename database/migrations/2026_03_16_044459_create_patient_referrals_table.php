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
        Schema::create('patient_referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pasien');
            $table->unsignedBigInteger('rs_asal');
            $table->unsignedBigInteger('rs_tujuan');
            $table->date('tanggal_rujukan');
            $table->text('alasan_rujukan')->nullable();
            $table->enum('status_rujukan', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending');
            $table->timestamps();

            $table->foreign('id_pasien')->references('id_pasien')->on('patients');
            $table->foreign('rs_asal')->references('id')->on('faskes');
            $table->foreign('rs_tujuan')->references('id')->on('faskes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_referrals');
    }
};
