<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('patients', function (Blueprint $table) {
    $table->id('id_pasien');
    $table->string('no_sep', 30);
    $table->string('no_kartu_bpjs', 30);
    $table->string('nama_pasien', 100);
    $table->string('no_telp', 20)->nullable();
    $table->string('fktp_asal', 100)->nullable();
    $table->unsignedBigInteger('created_by'); // penting: UNSIGNED!
    $table->timestamps();

    $table->foreign('created_by')
          ->references('id_user')
          ->on('users')
          ->onDelete('cascade');
});}

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
