<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
   protected $primaryKey = 'id_pasien';
    public $incrementing = true;
    protected $keyType = 'int';


    protected $fillable = [
        'no_sep',
        'no_kartu_bpjs',
        'no_kunjungan',
        'nama_pasien',
        'tanggal_lahir',
        'no_telp',
        'fktp_kode',
        'fktp_asal',
        'catatan',
        'file_upload',
        'created_by',
        'kode_apotek',
        'rumah_sakit_id',
    ];

    public function diagnosaPrb()
    {
        return $this->hasMany(DiagnosaPrb::class, 'id_pasien', 'id_pasien');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
