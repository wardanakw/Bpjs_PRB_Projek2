<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosa_prb', function (Blueprint $table) {
            $table->id('id_diagnosa');
            $table->unsignedBigInteger('id_pasien');
            $table->string('diagnosa', 100);
            $table->string('no_telp_pic', 20)->nullable();
            $table->date('tgl_pelayanan');
            $table->text('catatan')->nullable();
            $table->string('file_upload')->nullable();
            $table->timestamps();

            $table->foreign('id_pasien')
                ->references('id_pasien')
                ->on('patients')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosa_prb');
    }
};
