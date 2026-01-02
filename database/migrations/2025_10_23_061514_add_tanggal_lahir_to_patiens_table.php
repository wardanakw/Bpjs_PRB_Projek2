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
    Schema::table('patients', function (Blueprint $table) {
        $table->date('tanggal_lahir')->after('nama_pasien')->nullable();
    });
}

public function down(): void
{
    Schema::table('patients', function (Blueprint $table) {
        $table->dropColumn('tanggal_lahir');
    });
}

};
