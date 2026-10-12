<?php

namespace App\Http\Controllers\Eksekutif;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Tampilkan katalog portofolio proyek pengembangan aplikasi daerah.
     */
    public function index(Request $request): View
    {
        $query = Project::with(['aplikasi.instansi', 'tim.user']);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                    ->orWhereHas('aplikasi', function ($appQ) use ($search) {
                        $appQ->where('nama_aplikasi', 'like', "%{$search}%")
                            ->orWhereHas('instansi', function ($instQ) use ($search) {
                                $instQ->where('nama_instansi', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter Status Proyek
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter Instansi OPD
        if ($request->filled('instansi_id')) {
            $query->whereHas('aplikasi', function ($q) use ($request) {
                $q->where('instansi_id', $request->input('instansi_id'));
            });
        }

        $projects = $query->orderByDesc('updated_at')->paginate(10)->withQueryString();
        $instansis = Instansi::orderBy('nama_instansi')->get();

        // Ringkasan Status
        $counts = [
            'total' => Project::count(),
            'in_development' => Project::where('status', 'in_development')->count(),
            'testing' => Project::whereIn('status', ['functional_testing', 'security_testing'])->count(),
            'deployment' => Project::where('status', 'deployment')->count(),
            'live' => Project::where('status', 'live')->count(),
            'delayed' => Project::where('status', 'delayed')->count(),
        ];

        return view('eksekutif.project.index', compact('projects', 'instansis', 'counts'));
    }

    /**
     * Tampilkan detail portofolio proyek dan audit kendali mutu eksekutif.
     */
    public function show(Project $project): View
    {
        $project->load([
            'aplikasi.instansi',
            'tim.user',
            'tahapan' => fn ($q) => $q->orderBy('urutan'),
            'dokumenPerancangan',
            'testingAplikasi.penguji',
            'pengujianKeamanan.tester',
            'hostingAplikasi',
            'penundaan.pengaju',
            'rapat' => fn ($q) => $q->latest()->take(5),
        ]);

        return view('eksekutif.project.show', compact('project'));
    }
}
