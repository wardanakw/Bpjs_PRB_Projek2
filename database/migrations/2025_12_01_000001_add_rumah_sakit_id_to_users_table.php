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
        // Check if column doesn't exist to avoid duplicate column error
        if (!Schema::hasColumn('users', 'rumah_sakit_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('rumah_sakit_id')->nullable()->after('role');
                
                // Add foreign key constraint
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
