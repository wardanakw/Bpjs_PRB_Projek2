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
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'fktp_id')) {
            $table->unsignedBigInteger('fktp_id')->nullable()->after('role');

            $table->foreign('fktp_id')
                  ->references('id')
                  ->on('faskes')
                  ->onDelete('set null');
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['fktp_id']);
        $table->dropColumn('fktp_id');
    });
}

};
