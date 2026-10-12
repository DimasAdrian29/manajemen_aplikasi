<?php

namespace App\Http\Controllers\Tim;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\RapatProject;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard ringkasan penugasan tim pengembang.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Ambil semua proyek yang melibatkan pengguna ini (sebagai ketua atau anggota)
        $projects = Project::whereHas('tim', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['aplikasi.instansi', 'tim.user', 'tahapan'])
            ->latest()
            ->get();

        // Pisahkan proyek berdasarkan peran spesifik user pada proyek tersebut
        $leadProjects = $projects->filter(function ($project) use ($user) {
            return $project->tim->where('user_id', $user->id)->where('peran', 'ketua_tim')->isNotEmpty();
        });

        $memberProjects = $projects->filter(function ($project) use ($user) {
            return $project->tim->where('user_id', $user->id)->where('peran', 'anggota')->isNotEmpty();
        });

        // Statistik metrik ringkas
        $totalProjectsCount = $projects->count();
        $leadProjectsCount = $leadProjects->count();
        $memberProjectsCount = $memberProjects->count();
        $activeProjectsCount = $projects->whereNotIn('status', ['live', 'inactive'])->count();
        $completedProjectsCount = $projects->where('status', 'live')->count();

        // Agenda rapat mendatang yang mengundang user ini
        $upcomingRapats = RapatProject::whereHas('peserta', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with('project')
            ->where('waktu_rapat', '>=', now()->startOfDay())
            ->where('status', 'dijadwalkan')
            ->orderBy('waktu_rapat')
            ->take(4)
            ->get();

        return view('tim.dashboard', compact(
            'projects',
            'leadProjects',
            'memberProjects',
            'totalProjectsCount',
            'leadProjectsCount',
            'memberProjectsCount',
            'activeProjectsCount',
            'completedProjectsCount',
            'upcomingRapats'
        ));
    }
}
