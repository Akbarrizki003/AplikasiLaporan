<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 'tb_disposisi';
    protected $primaryKey = 'id_disposisi';

    protected $fillable = [
        'id_dokumen',
        'id_user',
        'status',
        'catatan',
    ];

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'id_dokumen');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}