<?php

namespace App\Models;

use Database\Factories\ProgresProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tahapan_project_id',
    'user_id',
    'persentase',
    'notulensi',
    'tanggal',
])]
class ProgresProject extends Model
{
    /** @use HasFactory<ProgresProjectFactory> */
    use HasFactory;

    protected $table = 'progres_project';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'persentase' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function tahapan(): BelongsTo
    {
        return $this->belongsTo(TahapanProject::class, 'tahapan_project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
