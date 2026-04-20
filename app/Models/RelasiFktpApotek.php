<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelasiFktpApotek extends Model
{
    use HasFactory;

    protected $table = 'relasi_fktp_apotek';
    protected $primaryKey = 'id_relasi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode_fktp',
        'nama_fktp',
        'nama_apotek',
        'kode_apotek',
    ];

    public function fktp()
    {
        return $this->belongsTo(Faskes::class, 'kode_fktp', 'kode_faskes');
    }


    public function apotek()
    {
        return $this->belongsTo(Faskes::class, 'kode_apotek', 'kode_faskes');
    }
}
