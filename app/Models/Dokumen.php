<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'tb_dokumen';
    protected $primaryKey = 'id_dokumen';

    protected $fillable = [
        'id_unit',
        'nama_dokumen',
        'tanggal_upload',
        'file',
        'status',
        'catatan',
    ];

    // Relasi dengan Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }
}
