<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard superadmin dengan metrik sistem.
     */
    public function index(): View
    {
        $stats = [
            'total_instansi' => Instansi::count(),
            'total_users' => User::count(),
            'total_anggota_tim' => User::whereHas('roles', fn ($q) => $q->whereIn('name', ['ketua_tim', 'anggota_tim']))->count(),
            'total_aplikasi' => Aplikasi::count(),
            'total_pengajuan' => Pengajuan::count(),
        ];

        $recentUsers = User::with(['roles', 'instansi'])
            ->latest()
            ->take(5)
            ->get();

        $recentAplikasi = Aplikasi::with('instansi')
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentUsers', 'recentAplikasi'));
    }
}
