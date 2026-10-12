<?php

namespace App\Http\Controllers\Infrastruktur;

use App\Http\Controllers\Controller;
use App\Models\HostingAplikasi;
use App\Models\Pengajuan;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the Infrastruktur Dashboard with operational metrics.
     */
    public function index(Request $request): View
    {
        $pendingDeploymentCount = Project::where('status', 'deployment')->count();
        $liveAppsCount = HostingAplikasi::where('status_hosting', 'aktif')->count();
        $maintenanceAppsCount = HostingAplikasi::where('status_hosting', 'maintenance')->count();

        $sslExpiringCount = HostingAplikasi::where('ssl_aktif', true)
            ->whereNotNull('ssl_kadaluarsa')
            ->whereBetween('ssl_kadaluarsa', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->count();

        $pendingDeactivationCount = Pengajuan::where('jenis_pengajuan', 'app_deactivation')
            ->where('status', 'approved')
            ->whereHas('aplikasi', function ($query) {
                $query->where('status', '!=', 'nonaktif');
            })
            ->count();

        $totalCpu = (int) HostingAplikasi::sum('cpu_core');
        $totalRam = (int) HostingAplikasi::sum('ram_gb');
        $totalStorage = (int) HostingAplikasi::sum('storage_gb');

        // Recent Deployments
        $recentDeployments = HostingAplikasi::with(['project.aplikasi.instansi', 'adminInfrastruktur'])
            ->latest()
            ->take(5)
            ->get();

        // Pending Projects ready for deployment
        $pendingProjects = Project::with(['aplikasi.instansi', 'pengajuan'])
            ->where('status', 'deployment')
            ->latest()
            ->take(5)
            ->get();

        // SSL Expiring soon list
        $sslExpiringList = HostingAplikasi::with(['project.aplikasi'])
            ->where('ssl_aktif', true)
            ->whereNotNull('ssl_kadaluarsa')
            ->whereBetween('ssl_kadaluarsa', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('ssl_kadaluarsa')
            ->take(5)
            ->get();

        return view('infrastruktur.dashboard', compact(
            'pendingDeploymentCount',
            'liveAppsCount',
            'maintenanceAppsCount',
            'sslExpiringCount',
            'pendingDeactivationCount',
            'totalCpu',
            'totalRam',
            'totalStorage',
            'recentDeployments',
            'pendingProjects',
            'sslExpiringList'
        ));
    }
}
