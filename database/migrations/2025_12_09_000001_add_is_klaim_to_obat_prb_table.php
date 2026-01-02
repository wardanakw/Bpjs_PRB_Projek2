<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('obat_prb', function (Blueprint $table) {
            if (!Schema::hasColumn('obat_prb', 'is_klaim')) {
                $table->boolean('is_klaim')->default(false);
            }
            if (!Schema::hasColumn('obat_prb', 'tanggal_klaim')) {
                $table->timestamp('tanggal_klaim')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('obat_prb', function (Blueprint $table) {
            if (Schema::hasColumn('obat_prb', 'is_klaim')) {
                $table->dropColumn('is_klaim');
            }
            if (Schema::hasColumn('obat_prb', 'tanggal_klaim')) {
                $table->dropColumn('tanggal_klaim');
            }
        });
    }
};
