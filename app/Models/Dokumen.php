<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;
    
    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'tb_dokumen';
    
    /**
     * Primary key yang digunakan oleh tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_dokumen';
    
    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'id_unit',
        'nama_dokumen',
        'tanggal_upload',
        'file',
        'status',
        'catatan',
    ];
    
    /**
     * Relasi ke model Unit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }
    
    /**
     * Mendapatkan status dalam format yang lebih mudah dibaca.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'dikirim' => 'Dikirim oleh Unit',
            'diterima_keuangan' => 'Diterima oleh Keuangan',
            'diteruskan_ke_manejer' => 'Diteruskan ke Manajer',
            'disetujui_manejer' => 'Disetujui oleh Manajer',
            'ditolak_manejer' => 'Ditolak oleh Manajer',
            'diteruskan_ke_atasan' => 'Diteruskan ke Atasan',
            'disetujui_atasan' => 'Disetujui oleh Atasan',
            'ditolak_atasan' => 'Ditolak oleh Atasan',
        ];
        
        return $statusLabels[$this->status] ?? $this->status;
    }
    
    /**
     * Mendapatkan warna badge status.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $statusColors = [
            'dikirim' => 'info',
            'diterima_keuangan' => 'primary',
            'diteruskan_ke_manejer' => 'warning',
            'disetujui_manejer' => 'success',
            'ditolak_manejer' => 'danger',
            'diteruskan_ke_atasan' => 'warning',
            'disetujui_atasan' => 'success',
            'ditolak_atasan' => 'danger',
        ];
        
        return $statusColors[$this->status] ?? 'secondary';
    }
}