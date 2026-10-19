<?php

use App\Http\Controllers\Aptika\DashboardController as AptikaDashboardController;
use App\Http\Controllers\Aptika\LayananController as AptikaLayananController;
use App\Http\Controllers\Aptika\PengajuanController as AptikaPengajuanController;
use App\Http\Controllers\Aptika\ProjectController as AptikaProjectController;
use App\Http\Controllers\Aptika\RapatController as AptikaRapatController;
use App\Http\Controllers\Aptika\TestingController as AptikaTestingController;
use App\Http\Controllers\Aptika\WorkflowController as AptikaWorkflowController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Eksekutif\AplikasiController as EksekutifAplikasiController;
use App\Http\Controllers\Eksekutif\DashboardController as EksekutifDashboardController;
use App\Http\Controllers\Eksekutif\LaporanController as EksekutifLaporanController;
use App\Http\Controllers\Eksekutif\PenundaanController as EksekutifPenundaanController;
use App\Http\Controllers\Eksekutif\ProjectController as EksekutifProjectController;
use App\Http\Controllers\Infrastruktur\DashboardController as InfrastrukturDashboardController;
use App\Http\Controllers\Infrastruktur\DeploymentController as InfrastrukturDeploymentController;
use App\Http\Controllers\Infrastruktur\HostingController as InfrastrukturHostingController;
use App\Http\Controllers\Infrastruktur\PenonaktifanController as InfrastrukturPenonaktifanController;
use App\Http\Controllers\Opd\AplikasiController as OpdAplikasiController;
use App\Http\Controllers\Opd\DashboardController as OpdDashboardController;
use App\Http\Controllers\Opd\PengajuanController as OpdPengajuanController;
use App\Http\Controllers\Persandian\DashboardController as PersandianDashboardController;
use App\Http\Controllers\Persandian\PengujianController as PersandianPengujianController;
use App\Http\Controllers\Superadmin\AnggotaTimController;
use App\Http\Controllers\Superadmin\AplikasiController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\InstansiController;
use App\Http\Controllers\Superadmin\MasterDokumenController;
use App\Http\Controllers\Superadmin\RoleController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Tim\DashboardController as TimDashboardController;
use App\Http\Controllers\Tim\ProjectController as TimProjectController;
use App\Http\Controllers\Tim\RapatController as TimRapatController;
use App\Http\Controllers\Workflow\WorkflowTaskController;
use Illuminate\Support\Facades\Route;

// Halaman utama: Jika sudah login arahkan ke dashboard peran, jika belum arahkan ke login
Route::get('/', function () {
    if (auth()->check()) {
        session()->forget('url.intended');

        return redirect()->to(auth()->user()->dashboardRoute());
    }

    return redirect()->route('login');
});

// Autentikasi (Tamu / Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout (Hanya User Terautentikasi)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Panel Superadmin
Route::middleware(['auth', 'role_or_permission:superadmin|rbac.user.view|rbac.role.view|rbac.matrix.manage|master.instansi.view|master.dokumen.view|master.aplikasi.create'])
    ->prefix('superadmin')
    ->as('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('role_or_permission:superadmin|rbac.user.view|rbac.role.view|rbac.matrix.manage');

        // Master Data
        Route::resource('instansi', InstansiController::class)->except(['show'])->middleware('role_or_permission:superadmin|master.instansi.view');
        Route::resource('anggota-tim', AnggotaTimController::class)->except(['show'])->parameters(['anggota-tim' => 'anggota_tim'])->middleware('role_or_permission:superadmin|rbac.user.view');
        Route::resource('user', UserController::class)->except(['show'])->middleware('role_or_permission:superadmin|rbac.user.view');
        Route::resource('aplikasi', AplikasiController::class)->except(['show'])->middleware('role_or_permission:superadmin|master.aplikasi.create');
        Route::resource('master-dokumen', MasterDokumenController::class)->except(['show'])->parameters(['master-dokumen' => 'master_dokumen'])->middleware('role_or_permission:superadmin|master.dokumen.view');
        Route::patch('master-dokumen/{master_dokumen}/toggle', [MasterDokumenController::class, 'toggleStatus'])->name('master-dokumen.toggle')->middleware('role_or_permission:superadmin|master.dokumen.edit');

        // Manajemen Peran & Matriks Hak Akses (Master User: Role & Permission)
        Route::resource('role', RoleController::class)->except(['create', 'show', 'edit'])->middleware('role_or_permission:superadmin|rbac.role.view');
        Route::get('permission', [RoleController::class, 'permissionsIndex'])->name('permission.index')->middleware('role_or_permission:superadmin|rbac.matrix.manage|rbac.role.view');
        Route::get('role/{role}/permissions', [RoleController::class, 'permissions'])->name('role.permissions')->middleware('role_or_permission:superadmin|rbac.matrix.manage');
        Route::put('role/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('role.permissions.update')->middleware('role_or_permission:superadmin|rbac.matrix.manage');
    });

Route::middleware(['auth', 'role_or_permission:admin_aptika|pengajuan.review|project.create|project.manage_tim|testing.fungsional.view|rapat.create|layanan.view|workflow.view'])
    ->prefix('aptika')
    ->as('aptika.')
    ->group(function () {
        Route::get('/dashboard', [AptikaDashboardController::class, 'index'])->name('dashboard');

        // Telaah Pengajuan Usulan OPD
        Route::get('/pengajuan', [AptikaPengajuanController::class, 'index'])->name('pengajuan.index')->middleware('role_or_permission:admin_aptika|pengajuan.review');
        Route::get('/pengajuan/{pengajuan}', [AptikaPengajuanController::class, 'show'])->name('pengajuan.show')->middleware('role_or_permission:admin_aptika|pengajuan.review');
        Route::patch('/pengajuan/{pengajuan}/review', [AptikaPengajuanController::class, 'review'])->name('pengajuan.review')->middleware('role_or_permission:admin_aptika|pengajuan.review');

        // Manajemen Proyek
        Route::resource('project', AptikaProjectController::class)->only(['index', 'show'])->middleware('role_or_permission:admin_aptika|project.view');
        Route::resource('project', AptikaProjectController::class)->only(['create', 'store'])->middleware('role_or_permission:admin_aptika|project.create');
        Route::patch('/project/{project}/status', [AptikaProjectController::class, 'updateStatus'])->name('project.status')->middleware('role_or_permission:admin_aptika|project.edit');
        Route::patch('/project/{project}/tim', [AptikaProjectController::class, 'updateTim'])->name('project.tim')->middleware('role_or_permission:admin_aptika|project.manage_tim');
        Route::patch('/project/{project}/dokumen/{dokumen}/review', [AptikaProjectController::class, 'reviewDokumen'])->name('project.dokumen.review')->middleware('role_or_permission:admin_aptika|project.review_dokumen');

        // Uji Fungsional Aplikasi
        Route::resource('testing', AptikaTestingController::class)->only(['index'])->middleware('role_or_permission:admin_aptika|testing.fungsional.view');
        Route::resource('testing', AptikaTestingController::class)->only(['create', 'store'])->middleware('role_or_permission:admin_aptika|testing.fungsional.create');

        // Manajemen Rapat Proyek & Notifikasi
        Route::resource('rapat', AptikaRapatController::class)->only(['index', 'show'])->middleware('role_or_permission:admin_aptika|rapat.view');
        Route::resource('rapat', AptikaRapatController::class)->only(['create', 'store'])->middleware('role_or_permission:admin_aptika|rapat.create');
        Route::patch('/rapat/{rapat}/notulensi', [AptikaRapatController::class, 'notulensi'])->name('rapat.notulensi')->middleware('role_or_permission:admin_aptika|rapat.notulensi');

        // Konfigurasi Layanan & Susunan Workflow Dinamis
        Route::resource('layanan', AptikaLayananController::class)->middleware('role_or_permission:admin_aptika|layanan.view');
        Route::patch('layanan/{layanan}/toggle', [AptikaLayananController::class, 'toggle'])->name('layanan.toggle')->middleware('role_or_permission:admin_aptika|layanan.edit');
        Route::post('layanan/{layanan}/workflow', [AptikaLayananController::class, 'attachWorkflow'])->name('layanan.workflow.attach')->middleware('role_or_permission:admin_aptika|layanan.edit');
        Route::delete('layanan/{layanan}/workflow/{layananWorkflow}', [AptikaLayananController::class, 'detachWorkflow'])->name('layanan.workflow.detach')->middleware('role_or_permission:admin_aptika|layanan.edit');
        Route::patch('layanan/{layanan}/workflow/reorder', [AptikaLayananController::class, 'reorderWorkflow'])->name('layanan.workflow.reorder')->middleware('role_or_permission:admin_aptika|layanan.edit');

        // Master Workflow & Konfigurasi Field
        Route::resource('workflow', AptikaWorkflowController::class)->middleware('role_or_permission:admin_aptika|workflow.view');
        Route::post('workflow/{workflow}/field', [AptikaWorkflowController::class, 'storeField'])->name('workflow.field.store')->middleware('role_or_permission:admin_aptika|workflow.edit');
        Route::put('workflow/{workflow}/field/{field}', [AptikaWorkflowController::class, 'updateField'])->name('workflow.field.update')->middleware('role_or_permission:admin_aptika|workflow.edit');
        Route::delete('workflow/{workflow}/field/{field}', [AptikaWorkflowController::class, 'destroyField'])->name('workflow.field.destroy')->middleware('role_or_permission:admin_aptika|workflow.edit');
        Route::patch('workflow/{workflow}/field/reorder', [AptikaWorkflowController::class, 'reorderField'])->name('workflow.field.reorder')->middleware('role_or_permission:admin_aptika|workflow.edit');
    });

Route::middleware(['auth', 'role_or_permission:admin_infrastruktur|infra.deployment.view|infra.hosting.view|infra.decommission.view'])
    ->prefix('infrastruktur')
    ->as('infrastruktur.')
    ->group(function () {
        Route::get('/dashboard', [InfrastrukturDashboardController::class, 'index'])->name('dashboard');

        // Antrean Deployment & Eksekusi Go-Live
        Route::get('/deployment', [InfrastrukturDeploymentController::class, 'index'])->name('deployment.index')->middleware('role_or_permission:admin_infrastruktur|infra.deployment.view');
        Route::get('/deployment/{project}/create', [InfrastrukturDeploymentController::class, 'create'])->name('deployment.create')->middleware('role_or_permission:admin_infrastruktur|infra.deployment.execute');
        Route::post('/deployment/{project}', [InfrastrukturDeploymentController::class, 'store'])->name('deployment.store')->middleware('role_or_permission:admin_infrastruktur|infra.deployment.execute');

        // Inventaris Server & Hosting
        Route::resource('hosting', InfrastrukturHostingController::class)->only(['index', 'show'])->middleware('role_or_permission:admin_infrastruktur|infra.hosting.view');
        Route::resource('hosting', InfrastrukturHostingController::class)->only(['edit', 'update'])->middleware('role_or_permission:admin_infrastruktur|infra.hosting.edit');

        // Antrean Penonaktifan Aplikasi & Decommissioning Server
        Route::get('/penonaktifan', [InfrastrukturPenonaktifanController::class, 'index'])->name('penonaktifan.index')->middleware('role_or_permission:admin_infrastruktur|infra.decommission.view');
        Route::post('/penonaktifan/{pengajuan}/execute', [InfrastrukturPenonaktifanController::class, 'execute'])->name('penonaktifan.execute')->middleware('role_or_permission:admin_infrastruktur|infra.decommission.execute');
    });

Route::middleware(['auth', 'role_or_permission:admin_persandian|security.audit.view|security.audit.create'])
    ->prefix('persandian')
    ->as('persandian.')
    ->group(function () {
        Route::get('/dashboard', [PersandianDashboardController::class, 'index'])->name('dashboard');

        // Pengujian Keamanan & Kelaikan Operasi (CSIRT)
        Route::get('/pengujian', [PersandianPengujianController::class, 'index'])->name('pengujian.index')->middleware('role_or_permission:admin_persandian|security.audit.view');
        Route::get('/pengujian/riwayat', [PersandianPengujianController::class, 'riwayat'])->name('pengujian.riwayat')->middleware('role_or_permission:admin_persandian|security.audit.view');
        Route::get('/pengujian/{project}/create', [PersandianPengujianController::class, 'create'])->name('pengujian.create')->middleware('role_or_permission:admin_persandian|security.audit.create');
        Route::post('/pengujian/{project}', [PersandianPengujianController::class, 'store'])->name('pengujian.store')->middleware('role_or_permission:admin_persandian|security.audit.create');
        Route::get('/pengujian/{pengujian}', [PersandianPengujianController::class, 'show'])->name('pengujian.show')->middleware('role_or_permission:admin_persandian|security.audit.view');
    });

Route::middleware(['auth', 'permission:pengajuan.view|pengajuan.create|master.aplikasi.view'])
    ->prefix('opd')
    ->as('opd.')
    ->group(function () {
        Route::get('/dashboard', [OpdDashboardController::class, 'index'])->name('dashboard');

        Route::middleware('permission:pengajuan.create')->group(function () {
            Route::get('/pengajuan/create', [OpdPengajuanController::class, 'create'])->name('pengajuan.create');
            Route::post('/pengajuan', [OpdPengajuanController::class, 'store'])->name('pengajuan.store');
        });

        Route::middleware('permission:pengajuan.view')->group(function () {
            Route::get('/pengajuan', [OpdPengajuanController::class, 'index'])->name('pengajuan.index');
            Route::get('/pengajuan/{pengajuan}', [OpdPengajuanController::class, 'show'])->name('pengajuan.show');
        });

        Route::delete('/pengajuan/{pengajuan}', [OpdPengajuanController::class, 'destroy'])
            ->middleware('permission:pengajuan.delete')
            ->name('pengajuan.destroy');

        Route::get('/aplikasi', [OpdAplikasiController::class, 'index'])
            ->middleware('permission:master.aplikasi.view')
            ->name('aplikasi.index');
    });

Route::middleware(['auth', 'role_or_permission:kepala_bidang|kepala_dinas|eksekutif.dashboard.view|eksekutif.penundaan.review|eksekutif.laporan.view|project.view'])
    ->prefix('eksekutif')
    ->as('eksekutif.')
    ->group(function () {
        Route::get('/dashboard', [EksekutifDashboardController::class, 'index'])->name('dashboard')->middleware('role_or_permission:kepala_bidang|kepala_dinas|eksekutif.dashboard.view');

        // Monitoring Portofolio Proyek
        Route::get('/project', [EksekutifProjectController::class, 'index'])->name('project.index')->middleware('role_or_permission:kepala_bidang|kepala_dinas|project.view|eksekutif.dashboard.view');
        Route::get('/project/{project}', [EksekutifProjectController::class, 'show'])->name('project.show')->middleware('role_or_permission:kepala_bidang|kepala_dinas|project.view|eksekutif.dashboard.view');

        // Persetujuan / Approval Penundaan Proyek (Khusus/Diutamakan Kabid)
        Route::get('/penundaan', [EksekutifPenundaanController::class, 'index'])->name('penundaan.index')->middleware('role_or_permission:kepala_bidang|kepala_dinas|eksekutif.penundaan.review');
        Route::post('/penundaan/{penundaan}/approve', [EksekutifPenundaanController::class, 'approve'])->name('penundaan.approve')->middleware('role_or_permission:kepala_bidang|kepala_dinas|eksekutif.penundaan.review');
        Route::post('/penundaan/{penundaan}/reject', [EksekutifPenundaanController::class, 'reject'])->name('penundaan.reject')->middleware('role_or_permission:kepala_bidang|kepala_dinas|eksekutif.penundaan.review');

        // Katalog Aplikasi Daerah
        Route::get('/aplikasi', [EksekutifAplikasiController::class, 'index'])->name('aplikasi.index')->middleware('role_or_permission:kepala_bidang|kepala_dinas|master.aplikasi.view');

        // Laporan Eksekutif (Print-Ready)
        Route::get('/laporan', [EksekutifLaporanController::class, 'index'])->name('laporan.index')->middleware('role_or_permission:kepala_bidang|kepala_dinas|eksekutif.laporan.view');
    });

Route::middleware(['auth', 'role_or_permission:ketua_tim|anggota_tim|project.view|project.update_progres|project.upload_dokumen|project.ajukan_penundaan|rapat.view|rapat.presensi'])
    ->prefix('tim')
    ->as('tim.')
    ->group(function () {
        Route::get('/dashboard', [TimDashboardController::class, 'index'])->name('dashboard');

        // Manajemen Proyek Tim
        Route::get('/project', [TimProjectController::class, 'index'])->name('project.index')->middleware('role_or_permission:ketua_tim|anggota_tim|project.view');
        Route::get('/project/{project}', [TimProjectController::class, 'show'])->name('project.show')->middleware('role_or_permission:ketua_tim|anggota_tim|project.view');
        Route::patch('/project/{project}/progres', [TimProjectController::class, 'updateProgres'])->name('project.progres')->middleware('role_or_permission:ketua_tim|anggota_tim|project.update_progres');
        Route::post('/project/{project}/tahapan', [TimProjectController::class, 'storeTahapan'])->name('project.tahapan')->middleware('role_or_permission:ketua_tim|anggota_tim|project.update_progres');
        Route::post('/project/{project}/dokumen', [TimProjectController::class, 'storeDokumen'])->name('project.dokumen')->middleware('role_or_permission:ketua_tim|anggota_tim|project.upload_dokumen');
        Route::post('/project/{project}/penundaan', [TimProjectController::class, 'storePenundaan'])->name('project.penundaan')->middleware('role_or_permission:ketua_tim|anggota_tim|project.ajukan_penundaan');

        // Rapat & Presensi
        Route::get('/rapat', [TimRapatController::class, 'index'])->name('rapat.index')->middleware('role_or_permission:ketua_tim|anggota_tim|rapat.view');
        Route::get('/rapat/{rapat}', [TimRapatController::class, 'show'])->name('rapat.show')->middleware('role_or_permission:ketua_tim|anggota_tim|rapat.view');
        Route::post('/rapat/{rapat}/presensi', [TimRapatController::class, 'presensi'])->name('rapat.presensi')->middleware('role_or_permission:ketua_tim|anggota_tim|rapat.presensi');
    });

// Antrean Tugas Alur Kerja Universal (Berlaku untuk seluruh peran sesuai alur tahapan aktif)
Route::middleware('auth')
    ->prefix('workflow-tasks')
    ->as('workflow.task.')
    ->group(function () {
        Route::get('/', [WorkflowTaskController::class, 'index'])->name('index');
        Route::get('/{project}', [WorkflowTaskController::class, 'show'])->name('show');
        Route::post('/{project}/advance', [WorkflowTaskController::class, 'advance'])->name('advance');
        Route::post('/{project}/revision', [WorkflowTaskController::class, 'revision'])->name('revision');
    });

require __DIR__.'/monitoring.php';
