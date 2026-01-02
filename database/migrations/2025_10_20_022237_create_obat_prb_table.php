<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('obat_prb', function (Blueprint $table) {
            $table->id('id_obat');
            $table->unsignedBigInteger('id_diagnosa');
            $table->string('nama_obat');
            $table->integer('jumlah_obat');
            $table->string('satuan')->nullable(); // contoh: tablet, kapsul
            $table->string('dosis_obat')->nullable(); // contoh: 3x1
            $table->string('aturan_pakai')->nullable(); // contoh: sesudah makan
            $table->timestamps();

            $table->foreign('id_diagnosa')->references('id_diagnosa')->on('diagnosa_prb')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_prb');
    }
};
