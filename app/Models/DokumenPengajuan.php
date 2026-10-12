<?php

namespace App\Models;

use Database\Factories\DokumenPengajuanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DokumenPengajuan extends Model
{
    /** @use HasFactory<DokumenPengajuanFactory> */
    use HasFactory;

    protected $table = 'dokumen_pengajuan';

    protected $fillable = [
        'pengajuan_id',
        'master_dokumen_id',
        'nama_file_asli',
        'file_path',
        'ukuran_file',
        'catatan',
    ];

    protected $casts = [
        'ukuran_file' => 'integer',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function masterDokumen(): BelongsTo
    {
        return $this->belongsTo(MasterDokumenPengajuan::class, 'master_dokumen_id');
    }

    public function getUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->ukuran_file;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
