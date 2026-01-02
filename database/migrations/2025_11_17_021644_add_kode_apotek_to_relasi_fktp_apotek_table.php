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
    Schema::table('relasi_fktp_apotek', function (Blueprint $table) {
        $table->string('kode_apotek')->nullable()->after('nama_apotek');
    });
}

public function down()
{
    Schema::table('relasi_fktp_apotek', function (Blueprint $table) {
        $table->dropColumn('kode_apotek');
    });
}

};
