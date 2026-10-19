<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class Workflow extends Model
{
    use HasFactory;

    protected $table = 'workflow';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'ikon',
        'role_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(WorkflowField::class, 'workflow_id')->orderBy('urutan');
    }

    public function layananWorkflows(): HasMany
    {
        return $this->hasMany(LayananWorkflow::class, 'workflow_id');
    }

    public function layanans(): BelongsToMany
    {
        return $this->belongsToMany(Layanan::class, 'layanan_workflow', 'workflow_id', 'layanan_id')
            ->withPivot(['id', 'urutan', 'is_active'])
            ->withTimestamps();
    }

    public function getTargetProjectStatusAttribute(): string
    {
        return match ($this->slug) {
            'pengajuan-berkas' => 'initiated',
            'telaah-kelayakan' => 'initiated',
            'in_development' => 'in_development',
            'functional_testing' => 'functional_testing',
            'security_testing' => 'security_testing',
            'deployment' => 'deployment',
            'decommissioning' => 'decommissioning',
            default => match ($this->role?->name) {
                'admin_persandian' => 'security_testing',
                'admin_infrastruktur' => 'deployment',
                'admin_aptika' => 'functional_testing',
                default => 'in_development',
            },
        };
    }
}
