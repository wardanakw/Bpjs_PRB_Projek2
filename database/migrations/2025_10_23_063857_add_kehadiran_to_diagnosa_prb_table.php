<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnosa_prb', function (Blueprint $table) {
            $table->string('kehadiran')->nullable()->after('status_prb');
        });
    }

    public function down(): void
    {
        Schema::table('diagnosa_prb', function (Blueprint $table) {
            $table->dropColumn('kehadiran');
        });
    }
};
