<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * Peran sistem yang dilindungi mutlak dari penghapusan / pengubahan nama.
     * Hanya peran 'superadmin' yang tidak boleh diubah atau dihapus.
     */
    protected array $protectedRoles = [
        'superadmin',
    ];

    /**
     * Tampilkan daftar peran (Roles).
     */
    public function index(): View
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->orderBy('id', 'asc')
            ->get();

        return view('superadmin.role.index', [
            'roles' => $roles,
            'protectedRoles' => $this->protectedRoles,
        ]);
    }

    /**
     * Simpan peran baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
        ], [
            'name.required' => 'Nama peran wajib diisi.',
            'name.unique' => 'Nama peran sudah digunakan dalam sistem.',
            'name.max' => 'Nama peran maksimal 50 karakter.',
        ]);

        $cleanName = Str::slug($request->input('name'), '_');

        Role::create([
            'name' => $cleanName,
            'guard_name' => 'web',
        ]);

        return redirect()->route('superadmin.role.index')
            ->with('success', "Peran '{$cleanName}' berhasil ditambahkan ke dalam sistem.");
    }

    /**
     * Perbarui nama peran.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'superadmin' || in_array($role->name, $this->protectedRoles, true)) {
            return redirect()->route('superadmin.role.index')
                ->with('error', "Peran sistem inti 'superadmin' tidak dapat diubah namanya.");
        }

        $request->validate([
            'name' => "required|string|max:50|unique:roles,name,{$role->id}",
        ], [
            'name.required' => 'Nama peran wajib diisi.',
            'name.unique' => 'Nama peran sudah digunakan dalam sistem.',
        ]);

        $cleanName = Str::slug($request->input('name'), '_');

        $role->update(['name' => $cleanName]);

        return redirect()->route('superadmin.role.index')
            ->with('success', "Peran berhasil diperbarui menjadi '{$cleanName}'.");
    }

    /**
     * Hapus peran dari sistem.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'superadmin' || in_array($role->name, $this->protectedRoles, true)) {
            return redirect()->route('superadmin.role.index')
                ->with('error', "Peran utama 'superadmin' dilindungi dan tidak dapat dihapus.");
        }

        $userCount = $role->users()->count();
        $roleName = $role->name;

        // Hapus peran (relasi pivot model_has_roles & role_has_permissions terhapus secara otomatis melalui cascade)
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $message = "Peran '{$roleName}' berhasil dihapus dari sistem.";
        if ($userCount > 0) {
            $message .= " ({$userCount} pengguna yang terhubung telah dilepas dari peran ini).";
        }

        return redirect()->route('superadmin.role.index')
            ->with('success', $message);
    }

    /**
     * Tampilkan antarmuka manajemen hak akses (Master User: Permission).
     * Menampilkan tabel peran dengan tombol aksi "Atur Permission".
     */
    public function permissionsIndex(): View
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->orderBy('id', 'asc')
            ->get();

        $totalPermissions = Permission::count();
        $systemRolesList = $this->protectedRoles;

        return view('superadmin.permission.index', [
            'roles' => $roles,
            'totalPermissions' => $totalPermissions,
            'systemRolesList' => $systemRolesList,
        ]);
    }

    /**
     * Tampilkan formulir matriks permission untuk peran tertentu.
     */
    public function permissions(Role $role): View
    {
        $allRoles = Role::orderBy('id', 'asc')->get();

        $modulesMeta = [
            'rbac' => [
                'name' => 'Modul 1: Manajemen Akses & Keamanan Sistem (RBAC)',
                'icon' => 'lock',
                'description' => 'Pengaturan peran, hak akses matrix, dan manajemen pengguna sistem.',
            ],
            'master_data' => [
                'name' => 'Modul 2: Master Data & Regulasi SPBE',
                'icon' => 'database',
                'description' => 'Pengelolaan data instansi OPD, katalog master aplikasi, dan syarat dokumen SPBE.',
            ],
            'pengajuan' => [
                'name' => 'Modul 3: Layanan Usulan & Pengajuan OPD',
                'icon' => 'description',
                'description' => 'Siklus permohonan aplikasi baru, pengembangan, dan telaah verifikasi APTIKA.',
            ],
            'project' => [
                'name' => 'Modul 4: Manajemen Proyek Pengembangan & Tim',
                'icon' => 'assignment',
                'description' => 'Inisiasi proyek, sprint milestone, susunan tim pengembang, dan dokumen perancangan teknis.',
            ],
            'testing_fungsional' => [
                'name' => 'Modul 5: Kendali Mutu & Uji Fungsional Aplikasi',
                'icon' => 'fact_check',
                'description' => 'Pencatatan sesi uji coba blackbox testing dan penerbitan berita acara fungsional.',
            ],
            'security_csirt' => [
                'name' => 'Modul 6: Audit Keamanan Informasi & CSIRT',
                'icon' => 'security',
                'description' => 'Audit penetration testing, asesmen kerentanan, rekomendasi risiko, dan sertifikasi CSIRT.',
            ],
            'infrastruktur' => [
                'name' => 'Modul 7: Infrastruktur, Server Hosting & Decommissioning',
                'icon' => 'dns',
                'description' => 'Eksekusi go-live, inventaris VM & server hosting, serta decommissioning penonaktifan aplikasi.',
            ],
            'rapat' => [
                'name' => 'Modul 8: Rapat Koordinasi, Presensi & Notulensi Proyek',
                'icon' => 'groups',
                'description' => 'Penjadwalan rapat koordinasi, presensi digital peserta, notulensi, dan unggah dokumentasi.',
            ],
            'eksekutif' => [
                'name' => 'Modul 9: Dasbor Eksekutif & Keputusan Strategis',
                'icon' => 'analytics',
                'description' => 'Monitoring makro eksekutif, persetujuan penundaan proyek, dan laporan eksekutif print-ready.',
            ],
            'audit_log' => [
                'name' => 'Modul 10: Audit Log, Monitoring & Notifikasi Sistem',
                'icon' => 'history',
                'description' => 'Jejak rekam audit aktivitas pengguna dan pusat notifikasi alur kerja.',
            ],
        ];

        $permissionsByModule = Permission::orderBy('id', 'asc')
            ->get()
            ->groupBy('module_group');

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('superadmin.role.permissions', [
            'role' => $role,
            'allRoles' => $allRoles,
            'modulesMeta' => $modulesMeta,
            'permissionsByModule' => $permissionsByModule,
            'rolePermissions' => $rolePermissions,
            'isSuperadmin' => $role->name === 'superadmin',
        ]);
    }

    /**
     * Simpan pembaruan matriks permission untuk peran tertentu.
     */
    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'superadmin') {
            return redirect()->route('superadmin.role.permissions', $role)
                ->with('info', "Peran 'superadmin' secara otomatis memiliki akses mutlak ke seluruh modul sistem.");
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $newPermissions = $validated['permissions'] ?? [];

        $role->syncPermissions($newPermissions);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('superadmin.role.permissions', $role)
            ->with('success', "Matriks hak akses untuk peran '{$role->name}' berhasil diperbarui (".count($newPermissions).' izin aktif).');
    }
}
