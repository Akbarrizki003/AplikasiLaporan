<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Jika nanti login sebagai unit
use Illuminate\Notifications\Notifiable;

class Unit extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tb_unit';
    protected $primaryKey = 'id_unit';

    protected $fillable = [
        'nama_unit',
        'direktur',
        'email',
        'telepon',
        'password',
        'logo',
    ];

    protected $hidden = [
        'password',
    ];
}
