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
        if (!Schema::hasTable('claims')) {
            Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('pharmacy_id')->constrained('faskes')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('patients', 'id_pasien')->nullOnDelete();
            $table->foreignId('diagnosa_id')->constrained('diagnosa_prb', 'id_diagnosa')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obat_prb', 'id_obat')->onDelete('cascade');
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('date_paid')->nullable();
            $table->string('proof_of_payment_file_path')->nullable(); // PDF bukti bayar dari apotek
            $table->string('fktp_file_path')->nullable(); // PDF dari FKTP (diagnosa)
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
