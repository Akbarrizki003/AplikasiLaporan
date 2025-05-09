<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Unit extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tb_unit';
    protected $primaryKey = 'id_unit';
    
    protected $fillable = [
        'nama_unit',
        'direktur',
        'id_user',
        'telepon',
        'logo'
    ];
    
    protected $hidden = [
        'password',
    ];

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_unit');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
