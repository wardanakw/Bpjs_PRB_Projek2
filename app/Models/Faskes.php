<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faskes extends Model
{
    use HasFactory;

    protected $table = 'faskes';

    protected $fillable = [
        'kode_faskes',
        'nama_faskes',
        'jenis_faskes',
        'alamat_faskes',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'nomor_pic',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

   
    public function apotekRelasi()
    {
        return $this->hasMany(RelasiFktpApotek::class, 'nama_fktp', 'nama_faskes');
    }

    public function fktpRelasi()
    {
        return $this->hasMany(RelasiFktpApotek::class, 'nama_apotek', 'nama_faskes');
    }
}
