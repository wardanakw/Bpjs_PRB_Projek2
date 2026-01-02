<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelasiFktpApotek extends Model
{
    use HasFactory;

    protected $table = 'relasi_fktp_apotek';

    protected $fillable = [
        'kode_fktp',
        'nama_fktp',
        'nama_apotek',
        'kode_apotek',
    ];

    public function fktp()
    {
        return $this->belongsTo(Faskes::class, 'nama_fktp', 'nama_faskes');
    }


    public function apotek()
    {
        return $this->belongsTo(Faskes::class, 'nama_apotek', 'nama_faskes');
    }
}
