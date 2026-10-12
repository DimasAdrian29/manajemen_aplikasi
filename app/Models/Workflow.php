<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory;

    protected $table = 'workflow';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'ikon',
    ];

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
}
