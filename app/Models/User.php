<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function disposisi()
    {
        return $this->hasMany(Disposisi::class, 'id_user');
    }

    public function isKeuangan()
    {
        return $this->role === 'keuangan';
    }

    public function isManajer()
    {
        return $this->role === 'manajer';
    }

    public function isAtasan()
    {
        return $this->role === 'atasan';
    }
    
    public function unit()
    {
        return $this->hasOne(Unit::class, 'id_user', 'id');
    }
    
    public function getIdUnitAttribute()
    {
        return $this->unit ? $this->unit->id_unit : null;
    }
}