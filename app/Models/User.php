<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'fktp_kode',
        'kode_apotek',
        'rumah_sakit_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = true;

    
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
   public function faskes()
{
    return $this->hasOne(Faskes::class, 'user_id', 'id_user');
}

    public function rumahSakit()
    {
        return $this->belongsTo(Faskes::class, 'rumah_sakit_id', 'id');
    }


}
