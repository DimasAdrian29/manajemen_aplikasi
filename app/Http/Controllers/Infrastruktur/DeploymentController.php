<?php

namespace App\Http\Controllers\Infrastruktur;

use App\Http\Controllers\Controller;
use App\Http\Requests\Infrastruktur\HostingRequest;
use App\Models\HostingAplikasi;
use App\Models\Project;
use App\Notifications\AplikasiLiveNotification;
use App\Services\Workflow\WorkflowTransitionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DeploymentController extends Controller
{
    /**
     * Display projects in deployment queue.
     */
    public function index(Request $request): View
    {
        $query = Project::with([
            'aplikasi.instansi',
            'pengajuan',
            'tim.user',
            'testingAplikasi',
            'pengujianKeamanan',
            'hostingAplikasi',
        ])->where('status', 'deployment');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                    ->orWhereHas('aplikasi', function ($appQ) use ($search) {
                        $appQ->where('nama_aplikasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('aplikasi.instansi', function ($instQ) use ($search) {
                        $instQ->where('nama_instansi', 'like', "%{$search}%");
                    });
            });
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return view('infrastruktur.deployment.index', compact('projects'));
    }

    public function __construct(
        protected WorkflowTransitionService $workflowService
    ) {}

    /**
     * Show deployment form for a project.
     */
    public function create(Project $project): View|RedirectResponse
    {
        if ($project->status !== 'deployment') {
            return redirect()
                ->route('infrastruktur.deployment.index')
                ->with('error', "Proyek '{$project->nama_project}' belum berada dalam tahap deployment.");
        }

        $project->load([
            'aplikasi.instansi',
            'pengajuan',
            'tim.user',
            'testingAplikasi',
            'pengujianKeamanan',
            'hostingAplikasi',
            'currentWorkflow.workflow.fields',
        ]);

        return view('infrastruktur.deployment.create', compact('project'));
    }

    /**
     * Store hosting information and trigger go-live transition.
     */
    public function store(HostingRequest $request, Project $project): RedirectResponse
    {
        if ($project->status !== 'deployment') {
            return redirect()
                ->route('infrastruktur.deployment.index')
                ->with('error', "Proyek '{$project->nama_project}' tidak dapat dideploy karena statusnya bukan deployment.");
        }

        $validated = $request->validated();

        $hosting = DB::transaction(function () use ($project, $validated, $request) {
            /** @var HostingAplikasi $hosting */
            $hosting = HostingAplikasi::updateOrCreate(
                ['project_id' => $project->id],
                array_merge($validated, [
                    'user_id' => auth()->id(),
                    'tanggal_live' => $validated['tanggal_live'] ?? now()->toDateString(),
                    'status_hosting' => $validated['status_hosting'] ?? 'aktif',
                ])
            );

            // Alur transisi dinamis proyek melalui WorkflowTransitionService
            $dynamicInput = [
                'fields' => $request->input('workflow_fields', []),
                'files' => $request->file('workflow_files', []),
            ];

            $this->workflowService->advance(
                $project,
                auth()->user(),
                '[Infrastruktur '.now()->format('d/m/Y H:i').'] Server & domain hosting berhasil dikonfigurasi.',
                $dynamicInput
            );

            // Update URL Aplikasi
            if ($project->aplikasi) {
                $project->aplikasi->update([
                    'url_aplikasi' => $hosting->url_aplikasi,
                ]);
            }

            // Update Pengajuan note
            if ($project->pengajuan) {
                $project->pengajuan->update([
                    'catatan_penilaian' => ($project->pengajuan->catatan_penilaian ? $project->pengajuan->catatan_penilaian."\n" : '').
                        '[Infrastruktur '.now()->format('d/m/Y H:i').'] Aplikasi berhasil dideploy dan berstatus LIVE.',
                ]);
            }

            // Send notification to OPD users
            $instansiUsers = $project->pengajuan?->instansi?->users;
            if ($instansiUsers && $instansiUsers->isNotEmpty()) {
                Notification::send($instansiUsers, new AplikasiLiveNotification($hosting));
            }

            // Send notification to Project Team Leader
            $ketuaTim = $project->tim()->where('peran', 'ketua_tim')->first()?->user;
            if ($ketuaTim && ! ($instansiUsers && $instansiUsers->contains('id', $ketuaTim->id))) {
                $ketuaTim->notify(new AplikasiLiveNotification($hosting));
            }

            return $hosting;
        });

        return redirect()
            ->route('infrastruktur.hosting.show', $hosting)
            ->with('success', "Aplikasi '{$project->nama_project}' berhasil dideploy dan berstatus LIVE!");
    }
}
