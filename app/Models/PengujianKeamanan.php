<?php

namespace App\Models;

use Database\Factories\PengujianKeamananFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'user_id',
    'kategori_pengujian',
    'file_hasil',
    'hasil',
    'tingkat_risiko',
    'keterangan',
    'rekomendasi',
    'nomor_surat',
    'catatan_kerentanan',
    'tanggal_uji',
])]
class PengujianKeamanan extends Model
{
    /** @use HasFactory<PengujianKeamananFactory> */
    use HasFactory;

    protected $table = 'pengujian_keamanan';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_uji' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
