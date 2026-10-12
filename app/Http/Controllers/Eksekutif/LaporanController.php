<?php

namespace App\Http\Controllers\Eksekutif;

use App\Http\Controllers\Controller;
use App\Models\Aplikasi;
use App\Models\HostingAplikasi;
use App\Models\Instansi;
use App\Models\PengujianKeamanan;
use App\Models\Project;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan lembar rekapitulasi laporan eksekutif siap cetak (Executive Summary).
     */
    public function index(): View
    {
        $totalAplikasi = Aplikasi::count();
        $aplikasiAktif = Aplikasi::where('status', 'aktif')->count();
        $aplikasiPengembangan = Aplikasi::where('status', 'dalam_pengembangan')->count();
        $aplikasiNonaktif = Aplikasi::where('status', 'tidak_aktif')->count();

        $totalProject = Project::count();
        $projectLive = Project::where('status', 'live')->count();
        $projectAktif = Project::whereIn('status', [
            'in_development',
            'functional_testing',
            'security_testing',
            'deployment',
        ])->count();
        $projectDelayed = Project::where('status', 'delayed')->count();

        // Rekapitulasi per OPD
        $rekapOpd = Instansi::withCount([
            'aplikasi as total_aplikasi',
            'aplikasi as aplikasi_aktif' => fn ($q) => $q->where('status', 'aktif'),
            'aplikasi as aplikasi_pengembangan' => fn ($q) => $q->where('status', 'dalam_pengembangan'),
            'aplikasi as aplikasi_nonaktif' => fn ($q) => $q->where('status', 'tidak_aktif'),
        ])
            ->having('total_aplikasi', '>', 0)
            ->orderByDesc('total_aplikasi')
            ->get();

        // Audit Keamanan CSIRT
        $totalAuditCsirt = PengujianKeamanan::count();
        $auditRekomendasi = PengujianKeamanan::where('hasil', 'lulus')->count();

        // Infrastruktur Hosting
        $totalHosting = HostingAplikasi::count();
        $hostingAktif = HostingAplikasi::where('status_hosting', 'aktif')->count();

        // Daftar Proyek Lengkap untuk Lampiran
        $projects = Project::with(['aplikasi.instansi', 'tim.user'])
            ->orderBy('status')
            ->get();

        return view('eksekutif.laporan.index', compact(
            'totalAplikasi',
            'aplikasiAktif',
            'aplikasiPengembangan',
            'aplikasiNonaktif',
            'totalProject',
            'projectLive',
            'projectAktif',
            'projectDelayed',
            'rekapOpd',
            'totalAuditCsirt',
            'auditRekomendasi',
            'totalHosting',
            'hostingAktif',
            'projects'
        ));
    }
}
