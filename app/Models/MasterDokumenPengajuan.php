<?php

namespace App\Models;

use Database\Factories\MasterDokumenPengajuanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDokumenPengajuan extends Model
{
    /** @use HasFactory<MasterDokumenPengajuanFactory> */
    use HasFactory;

    protected $table = 'master_dokumen_pengajuan';

    protected $fillable = [
        'nama_dokumen',
        'kode_dokumen',
        'deskripsi',
        'wajib',
        'berlaku_untuk',
        'format_diizinkan',
        'max_size_kb',
        'file_template',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'wajib' => 'boolean',
        'is_active' => 'boolean',
        'max_size_kb' => 'integer',
        'urutan' => 'integer',
    ];

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class, 'master_dokumen_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBerlakuUntuk($query, string $jenis)
    {
        return $query->where(function ($q) use ($jenis) {
            $q->where('berlaku_untuk', 'semua')
                ->orWhere('berlaku_untuk', $jenis);
        });
    }
}
