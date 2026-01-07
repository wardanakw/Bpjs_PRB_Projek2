<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rumah_sakit_id')) {
                $table->unsignedBigInteger('rumah_sakit_id')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'fktp_kode')) {
                $table->string('fktp_kode')->nullable()->after('rumah_sakit_id');
            }
            if (!Schema::hasColumn('users', 'kode_apotek')) {
                $table->string('kode_apotek')->nullable()->after('fktp_kode');
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'rumah_sakit_id')) {
                $table->unsignedBigInteger('rumah_sakit_id')->nullable()->after('fktp_asal');
            }
            if (!Schema::hasColumn('patients', 'fktp_kode')) {
                $table->string('fktp_kode')->nullable()->after('rumah_sakit_id');
            }
            if (!Schema::hasColumn('patients', 'kode_apotek')) {
                $table->string('kode_apotek')->nullable()->after('fktp_kode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'kode_apotek')) {
                $table->dropColumn('kode_apotek');
            }
            if (Schema::hasColumn('patients', 'fktp_kode')) {
                $table->dropColumn('fktp_kode');
            }
            if (Schema::hasColumn('patients', 'rumah_sakit_id')) {
                $table->dropColumn('rumah_sakit_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kode_apotek')) {
                $table->dropColumn('kode_apotek');
            }
            if (Schema::hasColumn('users', 'fktp_kode')) {
                $table->dropColumn('fktp_kode');
            }
            if (Schema::hasColumn('users', 'rumah_sakit_id')) {
                $table->dropColumn('rumah_sakit_id');
            }
        });
    }
};
