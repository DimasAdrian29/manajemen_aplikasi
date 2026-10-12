<?php

namespace App\Models;

use Database\Factories\PenundaanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'diajukan_oleh',
    'alasan',
    'file_surat',
    'status',
])]
class Penundaan extends Model
{
    /** @use HasFactory<PenundaanFactory> */
    use HasFactory;

    protected $table = 'penundaan';

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }
}
