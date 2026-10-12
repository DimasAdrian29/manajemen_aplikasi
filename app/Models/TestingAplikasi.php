<?php

namespace App\Models;

use Database\Factories\TestingAplikasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'penguji_id',
    'file_hasil',
    'keterangan',
    'tanggal_testing',
    'status_validasi',
    'catatan_validasi',
    'tanggal_validasi',
])]
class TestingAplikasi extends Model
{
    /** @use HasFactory<TestingAplikasiFactory> */
    use HasFactory;

    protected $table = 'testing_aplikasi';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_testing' => 'date',
            'tanggal_validasi' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function penguji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }
}
