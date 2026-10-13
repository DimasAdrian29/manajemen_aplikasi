<?php

namespace App\Models;

use Database\Factories\InstansiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_instansi', 'alamat', 'no_telepon', 'email'])]
class Instansi extends Model
{
    /** @use HasFactory<InstansiFactory> */
    use HasFactory;

    protected $table = 'instansi';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function aplikasi(): HasMany
    {
        return $this->hasMany(Aplikasi::class);
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }
}
