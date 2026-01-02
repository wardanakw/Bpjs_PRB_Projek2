<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosaPrb extends Model
{
    use HasFactory;

    protected $table = 'diagnosa_prb';
    protected $primaryKey = 'id_diagnosa';

    protected $fillable = [
        'id_pasien',
        'no_sep',
        'diagnosa',
        'status_prb',
        'no_telp_pic',
        'tgl_pelayanan',
        'catatan',
        'file_upload',
        'bukti_bayar_pdf'
    ];


    public function obatPrb()
    {
       
        return $this->hasMany(ObatPrb::class, 'id_diagnosa', 'id_diagnosa');
    }
       public function getTglPelayananFormattedAttribute()
    {
        return $this->tgl_pelayanan ? \Carbon\Carbon::parse($this->tgl_pelayanan)->format('Y-m-d') : null;
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_pasien', 'id_pasien');
    }
}
