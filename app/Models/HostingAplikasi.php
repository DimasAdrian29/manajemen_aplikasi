<?php

namespace App\Models;

use Database\Factories\HostingAplikasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'user_id',
    'nama_domain',
    'url_aplikasi',
    'ip_server',
    'nama_server',
    'jenis_hosting',
    'sistem_operasi',
    'cpu_core',
    'ram_gb',
    'storage_gb',
    'web_server',
    'database_server',
    'ssl_aktif',
    'ssl_kadaluarsa',
    'tanggal_live',
    'status_hosting',
    'spesifikasi_hosting',
    'keterangan',
])]
class HostingAplikasi extends Model
{
    /** @use HasFactory<HostingAplikasiFactory> */
    use HasFactory;

    protected $table = 'hosting_aplikasi';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ssl_aktif' => 'boolean',
            'ssl_kadaluarsa' => 'date',
            'tanggal_live' => 'date',
            'cpu_core' => 'integer',
            'ram_gb' => 'integer',
            'storage_gb' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function adminInfrastruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatHosting::class, 'hosting_aplikasi_id')->latest();
    }
}
