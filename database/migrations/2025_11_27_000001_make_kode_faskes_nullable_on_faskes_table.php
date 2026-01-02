<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faskes', function (Blueprint $table) {
            // Ubah kode_faskes dari required menjadi nullable
            // dan hilangkan unique constraint
            $table->string('kode_faskes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('faskes', function (Blueprint $table) {
            $table->string('kode_faskes')->unique()->change();
        });
    }
};
