<?php

namespace App\Models;

use Database\Factories\AplikasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[Fillable(['instansi_id', 'nama_aplikasi', 'deskripsi', 'url_aplikasi', 'status'])]
class Aplikasi extends Model
{
    /** @use HasFactory<AplikasiFactory> */
    use HasFactory;

    protected $table = 'aplikasi';

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class)->latestOfMany();
    }

    public function hostingAplikasi(): HasOneThrough
    {
        return $this->hasOneThrough(HostingAplikasi::class, Project::class);
    }
}
