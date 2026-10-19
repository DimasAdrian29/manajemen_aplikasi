<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Workflow\WorkflowFieldService;
use App\Services\Workflow\WorkflowTransitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkflowTaskController extends Controller
{
    public function __construct(
        protected WorkflowTransitionService $transitionService,
        protected WorkflowFieldService $fieldService
    ) {}

    /**
     * Tampilkan daftar antrean tugas alur kerja aktif yang ditugaskan ke peran pengguna aktif.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $userRoleIds = $user->roles->pluck('id')->toArray();
        $isSuperadmin = $user->hasRole('superadmin');
        $search = $request->query('search');

        $query = Project::with([
            'aplikasi.instansi',
            'pengajuan.layanan',
            'currentWorkflow.workflow.role',
            'currentWorkflow.workflow.fields',
            'tim.user',
        ])
            ->whereNotNull('current_workflow_id')
            ->where('status', '!=', 'live');

        // Jika bukan superadmin, hanya tampilkan proyek yang step aktifnya ditugaskan ke salah satu role milik user
        if (! $isSuperadmin) {
            $query->whereHas('currentWorkflow.workflow', function ($q) use ($userRoleIds) {
                $q->whereIn('role_id', $userRoleIds);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                    ->orWhereHas('aplikasi', function ($appQ) use ($search) {
                        $appQ->where('nama_aplikasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('currentWorkflow.workflow', function ($wfQ) use ($search) {
                        $wfQ->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $projects = $query->latest('updated_at')->paginate(10)->withQueryString();

        return view('workflow.task.index', compact('projects', 'isSuperadmin', 'search'));
    }

    /**
     * Tampilkan formulir eksekusi tahapan kerja dan input dinamis.
     */
    public function show(Project $project): View|RedirectResponse
    {
        $project->loadMissing([
            'aplikasi.instansi',
            'pengajuan.layanan',
            'currentWorkflow.workflow.role',
            'currentWorkflow.workflow.fields.masterDokumen',
            'tim.user',
            'pengajuan.workflowData',
        ]);

        if (! $project->currentWorkflow) {
            return redirect()->route('workflow.task.index')
                ->with('error', "Proyek '{$project->nama_project}' belum memiliki tahapan alur aktif.");
        }

        $workflow = $project->currentWorkflow->workflow;
        $assignedRoleId = $workflow->role_id;
        $user = auth()->user();

        // Validasi Otorisasi: Pengguna harus memiliki role yang ditugaskan atau superadmin
        if ($assignedRoleId && ! $user->roles->contains('id', $assignedRoleId) && ! $user->hasRole('superadmin')) {
            $roleName = $workflow->role?->name ?? 'peran lain';
            abort(403, "Akses ditolak. Tahapan '{$workflow->nama}' ditugaskan secara khusus untuk peran '{$roleName}'.");
        }

        $workflowData = $project->pengajuan?->workflowData
            ->firstWhere('layanan_workflow_id', $project->current_workflow_id);

        return view('workflow.task.show', compact('project', 'workflow', 'workflowData'));
    }

    /**
     * Selesaikan tahapan alur saat ini dan majukan proyek ke tahapan berikutnya (advance).
     */
    public function advance(Request $request, Project $project): RedirectResponse
    {
        $project->loadMissing(['currentWorkflow.workflow.role', 'currentWorkflow.workflow.fields']);

        if (! $project->currentWorkflow) {
            return redirect()->route('workflow.task.index')
                ->with('error', 'Proyek tidak memiliki tahapan alur aktif.');
        }

        $workflow = $project->currentWorkflow->workflow;
        $assignedRoleId = $workflow->role_id;
        $user = auth()->user();

        if ($assignedRoleId && ! $user->roles->contains('id', $assignedRoleId) && ! $user->hasRole('superadmin')) {
            abort(403, 'Anda tidak memiliki hak otorisasi untuk menyelesaikan tahapan alur ini.');
        }

        // Validasi field yang berstatus wajib
        $requiredTextFields = $workflow->fields->where('wajib', true)->whereIn('tipe', ['text', 'checklist']);
        $rules = [
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];

        foreach ($requiredTextFields as $field) {
            $rules["workflow_fields.{$field->id}"] = ['required'];
        }

        $requiredFileFields = $workflow->fields->where('wajib', true)->where('tipe', 'file_upload');
        foreach ($requiredFileFields as $field) {
            $rules["workflow_files.{$field->id}"] = ['required', 'file', 'max:20480'];
        }

        $request->validate($rules, [
            'workflow_fields.*.required' => 'Seluruh parameter/checklist wajib yang bertanda bintang harus dilengkapi.',
            'workflow_files.*.required' => 'Berkas persyaratan wajib diunggah sebelum tahapan diselesaikan.',
        ]);

        $dynamicInput = [
            'fields' => $request->input('workflow_fields', []),
            'files' => $request->file('workflow_files', []),
        ];

        $catatan = $request->input('catatan') ?: "Tahapan '{$workflow->nama}' berhasil diselesaikan oleh {$user->name}.";

        $nextWorkflow = $this->transitionService->advance(
            $project,
            $user,
            $catatan,
            $dynamicInput
        );

        $nextName = $nextWorkflow ? $nextWorkflow->workflow->nama : 'LIVE (Selesai Sepenuhnya)';

        return redirect()->route('workflow.task.index')
            ->with('success', "Tahapan '{$workflow->nama}' berhasil diselesaikan! Proyek '{$project->nama_project}' kini beralih ke: {$nextName}.");
    }

    /**
     * Kembalikan proyek ke tahapan revisi pengerjaan tim.
     */
    public function revision(Request $request, Project $project): RedirectResponse
    {
        $project->loadMissing(['currentWorkflow.workflow.role']);

        $workflow = $project->currentWorkflow?->workflow;
        $assignedRoleId = $workflow?->role_id;
        $user = auth()->user();

        if ($assignedRoleId && ! $user->roles->contains('id', $assignedRoleId) && ! $user->hasRole('superadmin')) {
            abort(403, 'Anda tidak memiliki hak otorisasi untuk mengembalikan proyek ini.');
        }

        $validated = $request->validate([
            'catatan_revisi' => ['required', 'string', 'max:2000'],
        ], [
            'catatan_revisi.required' => 'Catatan alasan perbaikan / revisi wajib diisi agar tim dapat menindaklanjuti temuan.',
        ]);

        $this->transitionService->sendForRevision(
            $project,
            $user,
            $validated['catatan_revisi'],
            'in_development'
        );

        return redirect()->route('workflow.task.index')
            ->with('warning', "Proyek '{$project->nama_project}' telah dikembalikan ke tim pengembang untuk perbaikan dengan catatan: {$validated['catatan_revisi']}");
    }
}
