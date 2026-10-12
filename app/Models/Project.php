<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'pengajuan_id',
    'aplikasi_id',
    'nama_project',
    'tanggal_mulai',
    'tanggal_selesai',
    'progres_total',
    'status',
    'current_workflow_id',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $table = 'project';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'progres_total' => 'integer',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function aplikasi(): BelongsTo
    {
        return $this->belongsTo(Aplikasi::class);
    }

    public function tim(): HasMany
    {
        return $this->hasMany(TimProject::class);
    }

    public function dokumenPerancangan(): HasMany
    {
        return $this->hasMany(DokumenPerancangan::class);
    }

    public function pengujianKeamanan(): HasMany
    {
        return $this->hasMany(PengujianKeamanan::class);
    }

    public function testingAplikasi(): HasMany
    {
        return $this->hasMany(TestingAplikasi::class);
    }

    public function hostingAplikasi(): HasOne
    {
        return $this->hasOne(HostingAplikasi::class);
    }

    public function penundaan(): HasMany
    {
        return $this->hasMany(Penundaan::class);
    }

    public function tahapan(): HasMany
    {
        return $this->hasMany(TahapanProject::class);
    }

    public function rapat(): HasMany
    {
        return $this->hasMany(RapatProject::class);
    }

    public function currentWorkflow(): BelongsTo
    {
        return $this->belongsTo(LayananWorkflow::class, 'current_workflow_id');
    }
}
