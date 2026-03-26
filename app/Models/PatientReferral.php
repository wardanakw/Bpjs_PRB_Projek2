<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientReferral extends Model
{
    protected $fillable = [
        'id_pasien',
        'rs_asal',
        'rs_tujuan',
        'tanggal_rujukan',
        'alasan_rujukan',
        'status_rujukan',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_pasien', 'id_pasien');
    }

    public function rsAsal()
    {
        return $this->belongsTo(Faskes::class, 'rs_asal', 'id');
    }

    public function rsTujuan()
    {
        return $this->belongsTo(Faskes::class, 'rs_tujuan', 'id');
    }
}
