<?php

use App\Http\Controllers\Aptika\DashboardController as AptikaDashboardController;
use App\Http\Controllers\Aptika\PengajuanController as AptikaPengajuanController;
use App\Http\Controllers\Aptika\ProjectController as AptikaProjectController;
use App\Http\Controllers\Aptika\RapatController as AptikaRapatController;
use App\Http\Controllers\Aptika\TestingController as AptikaTestingController;
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
Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->as('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::resource('instansi', InstansiController::class)->except(['show']);
        Route::resource('anggota-tim', AnggotaTimController::class)->except(['show'])->parameters(['anggota-tim' => 'anggota_tim']);
        Route::resource('user', UserController::class)->except(['show']);
        Route::resource('aplikasi', AplikasiController::class)->except(['show']);
        Route::resource('master-dokumen', MasterDokumenController::class)->except(['show'])->parameters(['master-dokumen' => 'master_dokumen']);
        Route::patch('master-dokumen/{master_dokumen}/toggle', [MasterDokumenController::class, 'toggleStatus'])->name('master-dokumen.toggle');

        // Manajemen Peran & Matriks Hak Akses (Master User: Role & Permission)
        Route::resource('role', RoleController::class)->except(['create', 'show', 'edit']);
        Route::get('permission', [RoleController::class, 'permissionsIndex'])->name('permission.index');
        Route::get('role/{role}/permissions', [RoleController::class, 'permissions'])->name('role.permissions');
        Route::put('role/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('role.permissions.update');
    });

Route::middleware(['auth', 'role:admin_aptika'])->prefix('aptika')->as('aptika.')->group(function () {
    Route::get('/dashboard', [AptikaDashboardController::class, 'index'])->name('dashboard');

    // Telaah Pengajuan Usulan OPD
    Route::get('/pengajuan', [AptikaPengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/{pengajuan}', [AptikaPengajuanController::class, 'show'])->name('pengajuan.show');
    Route::patch('/pengajuan/{pengajuan}/review', [AptikaPengajuanController::class, 'review'])->name('pengajuan.review');

    // Manajemen Proyek
    Route::resource('project', AptikaProjectController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/project/{project}/status', [AptikaProjectController::class, 'updateStatus'])->name('project.status');
    Route::patch('/project/{project}/tim', [AptikaProjectController::class, 'updateTim'])->name('project.tim');
    Route::patch('/project/{project}/dokumen/{dokumen}/review', [AptikaProjectController::class, 'reviewDokumen'])->name('project.dokumen.review');

    // Uji Fungsional Aplikasi
    Route::resource('testing', AptikaTestingController::class)->only(['index', 'create', 'store']);

    // Manajemen Rapat Proyek & Notifikasi
    Route::resource('rapat', AptikaRapatController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/rapat/{rapat}/notulensi', [AptikaRapatController::class, 'notulensi'])->name('rapat.notulensi');
});

Route::middleware(['auth', 'role:admin_infrastruktur'])->prefix('infrastruktur')->as('infrastruktur.')->group(function () {
    Route::get('/dashboard', [InfrastrukturDashboardController::class, 'index'])->name('dashboard');

    // Antrean Deployment & Eksekusi Go-Live
    Route::get('/deployment', [InfrastrukturDeploymentController::class, 'index'])->name('deployment.index');
    Route::get('/deployment/{project}/create', [InfrastrukturDeploymentController::class, 'create'])->name('deployment.create');
    Route::post('/deployment/{project}', [InfrastrukturDeploymentController::class, 'store'])->name('deployment.store');

    // Inventaris Server & Hosting
    Route::resource('hosting', InfrastrukturHostingController::class)->only(['index', 'show', 'edit', 'update']);

    // Antrean Penonaktifan Aplikasi & Decommissioning Server
    Route::get('/penonaktifan', [InfrastrukturPenonaktifanController::class, 'index'])->name('penonaktifan.index');
    Route::post('/penonaktifan/{pengajuan}/execute', [InfrastrukturPenonaktifanController::class, 'execute'])->name('penonaktifan.execute');
});

Route::middleware(['auth', 'role:admin_persandian'])->prefix('persandian')->as('persandian.')->group(function () {
    Route::get('/dashboard', [PersandianDashboardController::class, 'index'])->name('dashboard');

    // Pengujian Keamanan & Kelaikan Operasi (CSIRT)
    Route::get('/pengujian', [PersandianPengujianController::class, 'index'])->name('pengujian.index');
    Route::get('/pengujian/riwayat', [PersandianPengujianController::class, 'riwayat'])->name('pengujian.riwayat');
    Route::get('/pengujian/{project}/create', [PersandianPengujianController::class, 'create'])->name('pengujian.create');
    Route::post('/pengujian/{project}', [PersandianPengujianController::class, 'store'])->name('pengujian.store');
    Route::get('/pengujian/{pengujian}', [PersandianPengujianController::class, 'show'])->name('pengujian.show');
});

Route::middleware(['auth', 'role:opd'])->prefix('opd')->as('opd.')->group(function () {
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

Route::middleware(['auth', 'role:kepala_bidang|kepala_dinas'])->prefix('eksekutif')->as('eksekutif.')->group(function () {
    Route::get('/dashboard', [EksekutifDashboardController::class, 'index'])->name('dashboard');

    // Monitoring Portofolio Proyek
    Route::get('/project', [EksekutifProjectController::class, 'index'])->name('project.index');
    Route::get('/project/{project}', [EksekutifProjectController::class, 'show'])->name('project.show');

    // Persetujuan / Approval Penundaan Proyek (Khusus/Diutamakan Kabid)
    Route::get('/penundaan', [EksekutifPenundaanController::class, 'index'])->name('penundaan.index');
    Route::post('/penundaan/{penundaan}/approve', [EksekutifPenundaanController::class, 'approve'])->name('penundaan.approve');
    Route::post('/penundaan/{penundaan}/reject', [EksekutifPenundaanController::class, 'reject'])->name('penundaan.reject');

    // Katalog Aplikasi Daerah
    Route::get('/aplikasi', [EksekutifAplikasiController::class, 'index'])->name('aplikasi.index');

    // Laporan Eksekutif (Print-Ready)
    Route::get('/laporan', [EksekutifLaporanController::class, 'index'])->name('laporan.index');
});

Route::middleware(['auth', 'role:ketua_tim|anggota_tim'])->prefix('tim')->as('tim.')->group(function () {
    Route::get('/dashboard', [TimDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Proyek Tim
    Route::get('/project', [TimProjectController::class, 'index'])->name('project.index');
    Route::get('/project/{project}', [TimProjectController::class, 'show'])->name('project.show');
    Route::patch('/project/{project}/progres', [TimProjectController::class, 'updateProgres'])->name('project.progres');
    Route::post('/project/{project}/tahapan', [TimProjectController::class, 'storeTahapan'])->name('project.tahapan');
    Route::post('/project/{project}/dokumen', [TimProjectController::class, 'storeDokumen'])->name('project.dokumen');
    Route::post('/project/{project}/penundaan', [TimProjectController::class, 'storePenundaan'])->name('project.penundaan');

    // Rapat & Presensi
    Route::get('/rapat', [TimRapatController::class, 'index'])->name('rapat.index');
    Route::get('/rapat/{rapat}', [TimRapatController::class, 'show'])->name('rapat.show');
    Route::post('/rapat/{rapat}/presensi', [TimRapatController::class, 'presensi'])->name('rapat.presensi');
});
