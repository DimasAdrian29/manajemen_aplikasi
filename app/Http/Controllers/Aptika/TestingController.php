<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TestingAplikasi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TestingController extends Controller
{
    /**
     * Tampilkan riwayat dan daftar uji fungsional aplikasi.
     */
    public function index(Request $request): View
    {
        $statusValidasi = $request->query('status_validasi');

        $testings = TestingAplikasi::with(['project.aplikasi.instansi', 'penguji'])
            ->when($statusValidasi, function ($query, $statusValidasi) {
                $query->where('status_validasi', $statusValidasi);
            })
            ->latest('tanggal_testing')
            ->paginate(12)
            ->withQueryString();

        // Proyek yang siap atau sedang dalam tahap uji fungsional
        $readyProjects = Project::with(['aplikasi.instansi', 'tim.user'])
            ->whereIn('status', ['in_development', 'functional_testing'])
            ->get();

        return view('aptika.testing.index', compact('testings', 'readyProjects', 'statusValidasi'));
    }

    /**
     * Form input hasil uji fungsional untuk proyek tertentu.
     */
    public function create(Request $request): View
    {
        $selectedProjectId = $request->query('project_id');

        $projects = Project::with(['aplikasi.instansi'])
            ->whereIn('status', ['in_development', 'functional_testing', 'security_testing', 'initiated'])
            ->get();

        $selectedProject = null;
        if ($selectedProjectId) {
            $selectedProject = Project::with(['aplikasi.instansi'])->find($selectedProjectId);
        }

        return view('aptika.testing.create', compact('projects', 'selectedProject'));
    }

    /**
     * Simpan hasil audit & uji fungsional aplikasi, lalu perbarui status proyek.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'project_id' => ['required', 'exists:project,id'],
            'tanggal_testing' => ['required', 'date'],
            'status_validasi' => ['required', 'in:lulus,tidak_lulus,perbaikan'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'catatan_validasi' => ['nullable', 'string', 'max:2000'],
            'file_hasil' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar,xlsx', 'max:15360'],
        ]);

        return DB::transaction(function () use ($request) {
            $project = Project::findOrFail($request->project_id);

            $filePath = null;
            if ($request->hasFile('file_hasil')) {
                $file = $request->file('file_hasil');
                $filename = 'uji_fungsional_'.time().'.'.$file->getClientOriginalExtension();
                $filePath = $file->storeAs("testing/project_{$project->id}", $filename, 'public');
            }

            $testing = TestingAplikasi::create([
                'project_id' => $project->id,
                'penguji_id' => auth()->id(),
                'file_hasil' => $filePath,
                'keterangan' => $request->input('keterangan'),
                'tanggal_testing' => $request->input('tanggal_testing'),
                'status_validasi' => $request->input('status_validasi'),
                'catatan_validasi' => $request->input('catatan_validasi'),
                'tanggal_validasi' => Carbon::now(),
            ]);

            // Alur Transisi Proyek Berdasarkan Hasil Pengujian Fungsional
            if ($request->input('status_validasi') === 'lulus') {
                // Lulus Uji Fungsional -> Proyek maju ke Uji Keamanan (Admin Persandian)
                $project->update([
                    'status' => 'security_testing',
                    'progres_total' => max($project->progres_total, 75),
                ]);
                $msg = "Uji Fungsional LULUS! Proyek {$project->nama_project} otomatis diteruskan ke tahap Uji Keamanan Informasi (Bidang Persandian).";
            } elseif ($request->input('status_validasi') === 'perbaikan') {
                // Dikembalikan ke Tim Pengembang untuk perbaikan bug
                $project->update(['status' => 'in_development']);
                $msg = "Catatan perbaikan telah disimpan. Proyek {$project->nama_project} dikembalikan ke Tim Pengembang untuk revisi modul.";
            } else {
                $project->update(['status' => 'in_development']);
                $msg = "Hasil uji tidak lulus telah dicatat pada proyek {$project->nama_project}.";
            }

            return redirect()->route('aptika.testing.index')->with('success', $msg);
        });
    }
}
