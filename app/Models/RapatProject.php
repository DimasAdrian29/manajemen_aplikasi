<?php

namespace App\Models;

use Database\Factories\RapatProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'dibuat_oleh',
    'nama_rapat',
    'waktu_rapat',
    'tempat_rapat',
    'deskripsi',
    'file_surat_rapat',
    'file_dokumentasi',
    'notulensi',
    'status',
])]
class RapatProject extends Model
{
    /** @use HasFactory<RapatProjectFactory> */
    use HasFactory;

    protected $table = 'rapat_project';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_rapat' => 'datetime',
        ];
    }

    /**
     * Relasi ke Project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke User pembuat/penyelenggara rapat
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke daftar record PesertaRapat
     */
    public function peserta(): HasMany
    {
        return $this->hasMany(PesertaRapat::class, 'rapat_project_id');
    }

    /**
     * Relasi langsung Many-to-Many ke User peserta rapat
     */
    public function pesertaUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'peserta_rapat', 'rapat_project_id', 'user_id')
            ->withPivot('status_kehadiran', 'notifikasi_dikirim_at')
            ->withTimestamps();
    }
}
