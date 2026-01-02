<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'claim_number',
        'pharmacy_id',
        'patient_id',
        'diagnosa_id',
        'obat_id',
        'amount',
        'date_paid',
        'proof_of_payment_file_path',
        'fktp_file_path',
        'status',
        'notes',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'date_paid' => 'date',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Faskes::class, 'pharmacy_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function diagnosa()
    {
        return $this->belongsTo(DiagnosaPrb::class, 'diagnosa_id');
    }

    public function obat()
    {
        return $this->belongsTo(ObatPrb::class, 'obat_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
