<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowField extends Model
{
    use HasFactory;

    protected $table = 'workflow_field';

    protected $fillable = [
        'workflow_id',
        'label',
        'tipe',
        'master_dokumen_id',
        'wajib',
        'keterangan',
        'opsi',
        'urutan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wajib' => 'boolean',
            'urutan' => 'integer',
            'opsi' => 'array',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function masterDokumen(): BelongsTo
    {
        return $this->belongsTo(MasterDokumenPengajuan::class, 'master_dokumen_id');
    }

    public function fieldData(): HasMany
    {
        return $this->hasMany(PengajuanFieldData::class, 'workflow_field_id');
    }
}
