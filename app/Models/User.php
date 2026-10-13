<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'username', 'email', 'no_hp', 'jabatan', 'password', 'instansi_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Instansi (untuk OPD)
     */
    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    /**
     * Relasi ke penugasan tim proyek
     */
    public function timProjects(): HasMany
    {
        return $this->hasMany(TimProject::class);
    }

    /**
     * Relasi ke rapat yang dibuat/diselenggarakan oleh user
     */
    public function rapatDibuat(): HasMany
    {
        return $this->hasMany(RapatProject::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke daftar undangan rapat yang diterima user (model PesertaRapat)
     */
    public function undanganRapat(): HasMany
    {
        return $this->hasMany(PesertaRapat::class, 'user_id');
    }

    /**
     * Relasi langsung Many-to-Many ke RapatProject yang diikuti user
     */
    public function rapatDiikuti(): BelongsToMany
    {
        return $this->belongsToMany(RapatProject::class, 'peserta_rapat', 'user_id', 'rapat_project_id')
            ->withPivot('status_kehadiran', 'notifikasi_dikirim_at')
            ->withTimestamps();
    }

    /**
     * Dapatkan URL route dashboard sesuai peran dan hak akses (RBAC Dinamis).
     */
    public function dashboardRoute(): string
    {
        $roleNames = $this->roles->pluck('name')->toArray();

        // 1. Prioritas Utama: Peran Bawaan Sistem
        if (in_array('superadmin', $roleNames, true)) {
            return route('superadmin.dashboard');
        }

        if (in_array('admin_aptika', $roleNames, true)) {
            return route('aptika.dashboard');
        }

        if (in_array('admin_infrastruktur', $roleNames, true)) {
            return route('infrastruktur.dashboard');
        }

        if (in_array('admin_persandian', $roleNames, true)) {
            return route('persandian.dashboard');
        }

        if (in_array('opd', $roleNames, true)) {
            return route('opd.dashboard');
        }

        if (in_array('kepala_bidang', $roleNames, true) || in_array('kepala_dinas', $roleNames, true)) {
            return route('eksekutif.dashboard');
        }

        if (in_array('ketua_tim', $roleNames, true) || in_array('anggota_tim', $roleNames, true)) {
            return route('tim.dashboard');
        }

        // 2. Evaluasi Dinamis Berdasarkan Hak Akses Aktif (Untuk Peran Kustom Baru)
        if ($this->can('eksekutif.dashboard.view')) {
            return route('eksekutif.dashboard');
        }

        if ($this->canAny(['pengajuan.review', 'testing.fungsional.view'])) {
            return route('aptika.dashboard');
        }

        if ($this->canAny(['infra.deployment.view', 'infra.hosting.view', 'infra.decommission.view'])) {
            return route('infrastruktur.dashboard');
        }

        if ($this->canAny(['security.audit.view', 'security.audit.create'])) {
            return route('persandian.dashboard');
        }

        if ($this->canAny(['project.update_progres', 'project.upload_dokumen', 'rapat.presensi'])) {
            return route('tim.dashboard');
        }

        if ($this->canAny(['pengajuan.create', 'pengajuan.view'])) {
            return route('opd.dashboard');
        }

        if ($this->canAny(['rbac.user.view', 'rbac.role.view', 'rbac.matrix.manage'])) {
            return route('superadmin.dashboard');
        }

        if ($this->can('master.instansi.view')) {
            return route('superadmin.instansi.index');
        }

        if ($this->can('project.view')) {
            return route('aptika.project.index');
        }

        if ($this->can('master.aplikasi.view')) {
            return route('opd.aplikasi.index');
        }

        return route('login');
    }
}
