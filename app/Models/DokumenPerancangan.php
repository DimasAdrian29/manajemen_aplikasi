<?php

namespace App\Models;

use Database\Factories\DokumenPerancanganFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'dinilai_oleh',
    'jenis_dokumen',
    'file_dokumen',
    'status',
    'catatan',
    'tanggal_penilaian',
])]
class DokumenPerancangan extends Model
{
    /** @use HasFactory<DokumenPerancanganFactory> */
    use HasFactory;

    protected $table = 'dokumen_perancangan';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_penilaian' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }
}
