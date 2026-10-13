<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarisAplikasi extends Model
{
    use HasFactory;

    protected $table = 'inventaris_aplikasi';

    public const STATUS = [
        'aktif' => 'Aktif',
        'tidak_aktif' => 'Tidak Aktif',
        'diganti' => 'Diganti',
    ];

    public const PLATFORM = [
        'web' => 'Web',
        'mobile' => 'Mobile',
        'desktop' => 'Desktop',
    ];

    public const JENIS_LAYANAN = [
        'publik' => 'Publik',
        'pemerintahan' => 'Pemerintahan',
    ];

    public const KONEKTIVITAS = [
        'online' => 'Online',
        'offline' => 'Offline',
    ];

    public const STATUS_PENGEMBANGAN = [
        'sudah_selesai' => 'Sudah Selesai',
        'on_progress' => 'On Progress',
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'dibatalkan' => 'Dibatalkan',
    ];

    public const PENEMPATAN_SERVER = [
        'Diskominfotik',
        'PDN',
        'Biro PBJ',
    ];

    public const TIM_TEKNIS = [
        'programmer' => 'Programmer / Analis / Penanggung Jawab',
        'pengelola_adm' => 'Pengelola ADM',
        'tim_pengujian_aplikasi' => 'Tim Pengujian Aplikasi',
        'tim_pengujian_infrastruktur' => 'Tim Pengujian Infrastruktur',
        'tim_pengujian_keamanan' => 'Tim Pengujian Keamanan',
    ];

    public const DOKUMEN = [
        'ada_self_assessment' => 'Self Assessment',
        'ada_surat_keputusan' => 'Surat Keputusan',
        'ada_manual_book' => 'Manual Book',
        'ada_bast' => 'BAST',
    ];

    protected $fillable = [
        'nomor_registrasi',
        'nama_aplikasi',
        'instansi_id',
        'status',
        'aplikasi_pengganti_id',
        'is_aplikasi_pemprov',
        'is_website_resmi',
        'pic_aplikasi',
        'deskripsi',
        'fungsi_menu',
        'platform',
        'jenis_layanan',
        'konektivitas',
        'url',
        'tanggal_pemberlakuan',
        'penempatan_server',
        'programmer',
        'pengelola_adm',
        'tim_pengujian_aplikasi',
        'tim_pengujian_infrastruktur',
        'tim_pengujian_keamanan',
        'status_pengembangan',
        'ada_self_assessment',
        'ada_surat_keputusan',
        'ada_manual_book',
        'ada_bast',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nomor_registrasi' => 'integer',
            'is_aplikasi_pemprov' => 'boolean',
            'is_website_resmi' => 'boolean',
            'tanggal_pemberlakuan' => 'date',
            'ada_self_assessment' => 'boolean',
            'ada_surat_keputusan' => 'boolean',
            'ada_manual_book' => 'boolean',
            'ada_bast' => 'boolean',
        ];
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function aplikasiPengganti(): BelongsTo
    {
        return $this->belongsTo(self::class, 'aplikasi_pengganti_id');
    }

    public function aplikasiDigantikan(): HasMany
    {
        return $this->hasMany(self::class, 'aplikasi_pengganti_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? 'Belum Ditentukan';
    }

    public function getStatusPengembanganLabelAttribute(): ?string
    {
        return self::STATUS_PENGEMBANGAN[$this->status_pengembangan] ?? null;
    }

    public function getUrlLengkapAttribute(): ?string
    {
        if (! $this->url) {
            return null;
        }

        return str_starts_with($this->url, 'http') ? $this->url : 'https://'.$this->url;
    }

    /**
     * Pecah kolom tim teknis (satu nama per baris) menjadi array.
     *
     * @return array<int, string>
     */
    public function anggotaTim(string $kolom): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->{$kolom}))
            ->map(fn ($nama) => trim($nama))
            ->filter()
            ->values()
            ->all();
    }

    public function jumlahDokumenLengkap(): int
    {
        $jumlah = 0;

        foreach (array_keys(self::DOKUMEN) as $kolom) {
            $jumlah += $this->getAttribute($kolom) ? 1 : 0;
        }

        return $jumlah;
    }
}
