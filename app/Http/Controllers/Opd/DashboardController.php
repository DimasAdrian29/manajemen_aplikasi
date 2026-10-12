<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\Aplikasi;
use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan dashboard portal OPD.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $instansiId = $user->instansi_id;

        $stats = [
            'total_pengajuan' => Pengajuan::where('instansi_id', $instansiId)->count(),
            'menunggu_telaah' => Pengajuan::where('instansi_id', $instansiId)->whereIn('status', ['submitted', 'under_review'])->count(),
            'disetujui' => Pengajuan::where('instansi_id', $instansiId)->where('status', 'approved')->count(),
            'ditolak' => Pengajuan::where('instansi_id', $instansiId)->where('status', 'rejected')->count(),
            'total_aplikasi' => Aplikasi::where('instansi_id', $instansiId)->count(),
        ];

        $layanans = Layanan::where('is_active', true)->orderBy('urutan')->get();

        $recentPengajuan = Pengajuan::where('instansi_id', $instansiId)
            ->with(['layanan', 'aplikasi', 'dokumen.masterDokumen'])
            ->latest()
            ->take(5)
            ->get();

        return view('opd.dashboard', compact('user', 'stats', 'recentPengajuan', 'layanans'));
    }
}
