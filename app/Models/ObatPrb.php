<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObatPrb extends Model
{
    use HasFactory;

    protected $table = 'obat_prb';
    protected $primaryKey = 'id_obat';

    protected $fillable = [
        'id_diagnosa',
        'nama_obat',
        'jumlah_obat',
        'satuan',        
        'dosis_obat',
        'aturan_pakai',
        'catatan',
        'is_klaim',
        'tanggal_klaim',
        'bukti_bayar_pdf',
    ];

    public function diagnosaPrb()
    {
        return $this->belongsTo(DiagnosaPrb::class, 'id_diagnosa', 'id_diagnosa');
    }
}
