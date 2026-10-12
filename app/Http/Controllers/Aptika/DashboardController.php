<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\RapatProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan dashboard Bidang APTIKA.
     */
    public function index(Request $request): View
    {
        $stats = [
            'total_pengajuan' => Pengajuan::count(),
            'menunggu_telaah' => Pengajuan::where('status', 'submitted')->count(),
            'sedang_ditelaah' => Pengajuan::where('status', 'under_review')->count(),
            'disetujui' => Pengajuan::where('status', 'approved')->count(),
            'proyek_aktif' => Project::whereIn('status', ['initiated', 'in_development', 'functional_testing'])->count(),
            'uji_fungsional' => Project::where('status', 'functional_testing')->count(),
            'rapat_mendatang' => RapatProject::where('status', 'dijadwalkan')->where('waktu_rapat', '>=', Carbon::now())->count(),
        ];

        $pengajuanPending = Pengajuan::with(['instansi', 'dokumen'])
            ->whereIn('status', ['submitted', 'under_review'])
            ->latest()
            ->take(5)
            ->get();

        $proyekTerkini = Project::with(['aplikasi', 'pengajuan.instansi', 'tim.user'])
            ->latest()
            ->take(5)
            ->get();

        $rapatMendatang = RapatProject::with(['project.aplikasi', 'peserta.user'])
            ->where('status', 'dijadwalkan')
            ->where('waktu_rapat', '>=', Carbon::now())
            ->orderBy('waktu_rapat', 'asc')
            ->take(3)
            ->get();

        return view('aptika.dashboard', compact('stats', 'pengajuanPending', 'proyekTerkini', 'rapatMendatang'));
    }
}
