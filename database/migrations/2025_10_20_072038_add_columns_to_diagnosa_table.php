<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnosa_prb', function (Blueprint $table) {
            if (!Schema::hasColumn('diagnosa_prb', 'status_prb')) {
                $table->string('status_prb')->nullable()->after('diagnosa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('diagnosa_prb', function (Blueprint $table) {
            if (Schema::hasColumn('diagnosa_prb', 'status_prb')) {
                $table->dropColumn('status_prb');
            }
        });
    }
};
