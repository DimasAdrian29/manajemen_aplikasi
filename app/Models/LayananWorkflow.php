<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LayananWorkflow extends Model
{
    use HasFactory;

    protected $table = 'layanan_workflow';

    protected $fillable = [
        'layanan_id',
        'workflow_id',
        'urutan',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function pengajuanWorkflowData(): HasMany
    {
        return $this->hasMany(PengajuanWorkflowData::class, 'layanan_workflow_id');
    }
}
