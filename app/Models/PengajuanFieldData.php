<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PengajuanFieldData extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_field_data';

    protected $fillable = [
        'pengajuan_workflow_data_id',
        'workflow_field_id',
        'value',
        'file_path',
        'nama_file_asli',
        'ukuran_file',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ukuran_file' => 'integer',
        ];
    }

    public function pengajuanWorkflowData(): BelongsTo
    {
        return $this->belongsTo(PengajuanWorkflowData::class, 'pengajuan_workflow_data_id');
    }

    public function workflowField(): BelongsTo
    {
        return $this->belongsTo(WorkflowField::class, 'workflow_field_id');
    }

    public function getUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->ukuran_file ?? 0;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
