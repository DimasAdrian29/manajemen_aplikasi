<?php

namespace App\Models;

use Database\Factories\TahapanProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'nama_tahapan',
    'urutan',
    'persentase',
    'status',
])]
class TahapanProject extends Model
{
    /** @use HasFactory<TahapanProjectFactory> */
    use HasFactory;

    protected $table = 'tahapan_project';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'persentase' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function progres(): HasMany
    {
        return $this->hasMany(ProgresProject::class);
    }
}
