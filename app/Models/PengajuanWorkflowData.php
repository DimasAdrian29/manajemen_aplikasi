<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanWorkflowData extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_workflow_data';

    protected $fillable = [
        'pengajuan_id',
        'layanan_workflow_id',
        'status',
        'completed_by',
        'completed_at',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function layananWorkflow(): BelongsTo
    {
        return $this->belongsTo(LayananWorkflow::class, 'layanan_workflow_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function fieldData(): HasMany
    {
        return $this->hasMany(PengajuanFieldData::class, 'pengajuan_workflow_data_id');
    }
}
