<?php

namespace App\Models;

use Database\Factories\PengajuanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'kode_pengajuan',
    'dinilai_oleh',
    'instansi_id',
    'aplikasi_id',
    'jenis_pengajuan',
    'nama_aplikasi',
    'deskripsi_kebutuhan',
    'nama_pemohon',
    'no_hp_pemohon',
    'file_proposal',
    'file_surat_pengantar',
    'status',
    'catatan_penilaian',
    'tanggal_penilaian',
])]
class Pengajuan extends Model
{
    /** @use HasFactory<PengajuanFactory> */
    use HasFactory;

    protected $table = 'pengajuan';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_penilaian' => 'datetime',
        ];
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function aplikasi(): BelongsTo
    {
        return $this->belongsTo(Aplikasi::class);
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class, 'pengajuan_id');
    }
}
