<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('relasi_fktp_apotek', function (Blueprint $table) {
        $table->id('id_relasi');
        $table->string('kode_fktp', 50);
        $table->string('nama_fktp', 255);
        $table->string('nama_apotek', 255);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relasi_fktp_apoteks');
    }
};
