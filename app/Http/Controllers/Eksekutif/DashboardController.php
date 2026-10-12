<?php

namespace App\Http\Controllers\Eksekutif;

use App\Http\Controllers\Controller;
use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Penundaan;
use App\Models\Project;
use App\Models\RapatProject;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dasbor eksekutif pimpinan (Kadis & Kabid).
     */
    public function index(): View
    {
        $user = auth()->user();
        $isKadis = $user->hasRole('kepala_dinas');
        $isKabid = $user->hasRole('kepala_bidang');

        // Statistik Makro Aplikasi Daerah
        $totalAplikasi = Aplikasi::count();
        $aplikasiAktif = Aplikasi::where('status', 'aktif')->count();
        $aplikasiPengembangan = Aplikasi::where('status', 'dalam_pengembangan')->count();
        $aplikasiNonaktif = Aplikasi::where('status', 'tidak_aktif')->count();

        // Statistik Portofolio Proyek
        $totalProjects = Project::count();
        $projectsBerjalan = Project::whereIn('status', [
            'in_development',
            'functional_testing',
            'security_testing',
            'deployment',
        ])->count();
        $projectsLive = Project::where('status', 'live')->count();
        $projectsTertunda = Project::where('status', 'delayed')->count();

        // Rata-rata Progres Proyek Aktif
        $avgProgres = (int) round(Project::whereIn('status', [
            'in_development',
            'functional_testing',
            'security_testing',
            'deployment',
        ])->avg('progres_total') ?? 0);

        // Permohonan Penundaan yang Memerlukan Disposisi / Approval
        $penundaanPendingCount = Penundaan::where('status', 'diajukan')->count();
        $penundaanTerbaru = Penundaan::where('status', 'diajukan')
            ->with(['project.aplikasi.instansi', 'pengaju'])
            ->latest()
            ->take(5)
            ->get();

        // Daftar Proyek Aktif untuk Monitoring Cepat
        $proyekTerbaru = Project::with(['aplikasi.instansi', 'tim.user'])
            ->latest()
            ->take(6)
            ->get();

        // Sebaran Aplikasi per OPD (Top 6 Instansi)
        $sebaranOpd = Instansi::withCount('aplikasi')
            ->orderByDesc('aplikasi_count')
            ->take(6)
            ->get();

        // Agenda Rapat Koordinasi Mendatang
        $rapatMendatang = RapatProject::with(['project.aplikasi'])
            ->where('waktu_rapat', '>=', now()->subHours(2))
            ->orderBy('waktu_rapat')
            ->take(4)
            ->get();

        return view('eksekutif.dashboard', compact(
            'isKadis',
            'isKabid',
            'totalAplikasi',
            'aplikasiAktif',
            'aplikasiPengembangan',
            'aplikasiNonaktif',
            'totalProjects',
            'projectsBerjalan',
            'projectsLive',
            'projectsTertunda',
            'avgProgres',
            'penundaanPendingCount',
            'penundaanTerbaru',
            'proyekTerbaru',
            'sebaranOpd',
            'rapatMendatang'
        ));
    }
}
