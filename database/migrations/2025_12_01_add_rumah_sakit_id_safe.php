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
        // Cek apakah kolom sudah ada
        if (!Schema::hasColumn('users', 'rumah_sakit_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('rumah_sakit_id')->nullable()->after('role');
                
                // Foreign key constraint
                $table->foreign('rumah_sakit_id')
                    ->references('id')
                    ->on('faskes')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'rumah_sakit_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeignIdFor('faskes');
                $table->dropColumn('rumah_sakit_id');
            });
        }
    }
};
