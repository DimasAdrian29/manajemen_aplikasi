<?php

namespace App\Http\Controllers\Persandian;

use App\Http\Controllers\Controller;
use App\Models\PengujianKeamanan;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the CSIRT & Persandian Dashboard with security audit metrics.
     */
    public function index(Request $request): View
    {
        $pendingSecurityTestingCount = Project::where('status', 'security_testing')->count();
        $passedSecurityTestingCount = PengujianKeamanan::where('hasil', 'lulus')->count();
        $needRevisionCount = PengujianKeamanan::where('hasil', 'perbaikan')->count();
        $criticalRisksCount = PengujianKeamanan::whereIn('tingkat_risiko', ['tinggi', 'kritis'])->count();

        // Projects waiting for security audit
        $pendingProjects = Project::with([
            'aplikasi.instansi',
            'pengajuan',
            'tim.user',
            'testingAplikasi',
        ])
            ->where('status', 'security_testing')
            ->latest()
            ->take(6)
            ->get();

        // Recent audit records
        $recentAudits = PengujianKeamanan::with([
            'project.aplikasi.instansi',
            'tester',
        ])
            ->latest()
            ->take(5)
            ->get();

        // Risk distribution counts
        $riskStats = [
            'kritis' => PengujianKeamanan::where('tingkat_risiko', 'kritis')->count(),
            'tinggi' => PengujianKeamanan::where('tingkat_risiko', 'tinggi')->count(),
            'sedang' => PengujianKeamanan::where('tingkat_risiko', 'sedang')->count(),
            'rendah' => PengujianKeamanan::where('tingkat_risiko', 'rendah')->count(),
            'aman' => PengujianKeamanan::where('tingkat_risiko', 'aman')->count(),
        ];

        return view('persandian.dashboard', compact(
            'pendingSecurityTestingCount',
            'passedSecurityTestingCount',
            'needRevisionCount',
            'criticalRisksCount',
            'pendingProjects',
            'recentAudits',
            'riskStats'
        ));
    }
}
