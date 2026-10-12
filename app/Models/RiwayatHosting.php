<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'hosting_aplikasi_id',
    'user_id',
    'data_sebelum',
    'alasan_perubahan',
])]
class RiwayatHosting extends Model
{
    use HasFactory;

    protected $table = 'riwayat_hosting';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_sebelum' => 'array',
        ];
    }

    public function hostingAplikasi(): BelongsTo
    {
        return $this->belongsTo(HostingAplikasi::class, 'hosting_aplikasi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
