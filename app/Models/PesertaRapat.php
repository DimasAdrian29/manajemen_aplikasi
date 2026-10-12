<?php

namespace App\Models;

use Database\Factories\PesertaRapatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'rapat_project_id',
    'user_id',
    'status_kehadiran',
    'notifikasi_dikirim_at',
])]
class PesertaRapat extends Model
{
    /** @use HasFactory<PesertaRapatFactory> */
    use HasFactory;

    protected $table = 'peserta_rapat';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notifikasi_dikirim_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke RapatProject
     */
    public function rapat(): BelongsTo
    {
        return $this->belongsTo(RapatProject::class, 'rapat_project_id');
    }

    /**
     * Relasi ke User peserta
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
