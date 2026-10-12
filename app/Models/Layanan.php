<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'ikon',
        'warna',
        'is_active',
        'urutan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(LayananWorkflow::class, 'layanan_id')->orderBy('urutan');
    }

    public function workflowItems(): BelongsToMany
    {
        return $this->belongsToMany(Workflow::class, 'layanan_workflow', 'layanan_id', 'workflow_id')
            ->withPivot(['id', 'urutan', 'is_active'])
            ->withTimestamps()
            ->orderByPivot('urutan');
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'layanan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
