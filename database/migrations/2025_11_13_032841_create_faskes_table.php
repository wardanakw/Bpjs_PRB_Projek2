<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('faskes')) {
            Schema::create('faskes', function (Blueprint $table) {
                $table->id();
                $table->string('kode_faskes')->unique();
                $table->string('nama_faskes');
                $table->string('jenis_faskes');
                $table->text('alamat_faskes');
                $table->string('kecamatan')->nullable();
                $table->string('kabupaten')->nullable();
                $table->string('provinsi')->nullable();
                $table->string('kode_pos')->nullable();
                $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faskes');
    }
};
