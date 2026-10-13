<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TestingAplikasi;
use App\Services\Workflow\WorkflowTransitionService;
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

    public function __construct(
        protected WorkflowTransitionService $workflowService
    ) {}

    /**
     * Form input hasil uji fungsional untuk proyek tertentu.
     */
    public function create(Request $request): View
    {
        $selectedProjectId = $request->query('project_id');

        $projects = Project::with(['aplikasi.instansi', 'currentWorkflow.workflow.fields', 'pengajuan'])
            ->whereIn('status', ['in_development', 'functional_testing', 'security_testing', 'initiated'])
            ->get();

        $selectedProject = null;
        if ($selectedProjectId) {
            $selectedProject = Project::with(['aplikasi.instansi', 'currentWorkflow.workflow.fields', 'pengajuan'])->find($selectedProjectId);
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
            $project = Project::with(['currentWorkflow.workflow', 'pengajuan'])->findOrFail($request->project_id);

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

            // Alur Transisi Dinamis Proyek Berdasarkan Pipeline Layanan
            if ($request->input('status_validasi') === 'lulus') {
                $dynamicInput = [
                    'fields' => $request->input('workflow_fields', []),
                    'files' => $request->file('workflow_files', []),
                ];

                $nextWorkflow = $this->workflowService->advance(
                    $project,
                    auth()->user(),
                    $request->input('catatan_validasi') ?: 'Uji Fungsional LULUS.',
                    $dynamicInput
                );

                if ($nextWorkflow) {
                    $msg = "Uji Fungsional LULUS! Proyek {$project->nama_project} otomatis diteruskan ke tahap: {$nextWorkflow->workflow->nama}.";
                } else {
                    $msg = "Uji Fungsional LULUS! Seluruh tahapan alur proyek {$project->nama_project} telah selesai (LIVE).";
                }
            } elseif ($request->input('status_validasi') === 'perbaikan') {
                $this->workflowService->sendForRevision(
                    $project,
                    auth()->user(),
                    $request->input('catatan_validasi') ?: 'Perbaikan diperlukan dari hasil uji fungsional.'
                );
                $msg = "Catatan perbaikan telah disimpan. Proyek {$project->nama_project} dikembalikan ke Tim Pengembang untuk revisi modul.";
            } else {
                $this->workflowService->sendForRevision(
                    $project,
                    auth()->user(),
                    $request->input('catatan_validasi') ?: 'Uji fungsional tidak lulus.'
                );
                $msg = "Hasil uji tidak lulus telah dicatat pada proyek {$project->nama_project}.";
            }

            return redirect()->route('aptika.testing.index')->with('success', $msg);
        });
    }
}
