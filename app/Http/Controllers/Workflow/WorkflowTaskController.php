<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
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
     * Tampilkan daftar antrean tugas alur kerja aktif terpadu untuk peran pengguna aktif,
     * mencakup berkas permohonan masuk (telaah/inisiasi) dan tahapan proyek berjalan.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $userRoleIds = $user->roles->pluck('id')->toArray();
        $isSuperadmin = $user->hasRole('superadmin');
        $search = $request->query('search');

        // 1. Antrean Berkas Permohonan Masuk (Pengajuan Usulan OPD & Penonaktifan)
        $pengajuans = collect();

        // Admin APTIKA & Superadmin: Menangani telaah usulan dan inisiasi proyek baru
        if ($isSuperadmin || $user->hasRole('admin_aptika')) {
            $aptikaPengajuanQuery = Pengajuan::with(['layanan', 'instansi', 'aplikasi'])
                ->where(function ($q) {
                    $q->whereIn('status', ['submitted', 'under_review'])
                        ->orWhere(function ($approvedQ) {
                            $approvedQ->where('status', 'approved')
                                ->whereIn('jenis_pengajuan', ['new_app', 'app_development'])
                                ->doesntHave('project');
                        });
                });

            if ($search) {
                $aptikaPengajuanQuery->where(function ($q) use ($search) {
                    $q->where('kode_pengajuan', 'like', "%{$search}%")
                        ->orWhere('nama_aplikasi', 'like', "%{$search}%")
                        ->orWhereHas('instansi', fn ($iq) => $iq->where('nama_instansi', 'like', "%{$search}%"));
                });
            }

            $pengajuans = $pengajuans->merge($aptikaPengajuanQuery->latest()->get());
        }

        // Admin Infrastruktur & Superadmin: Menangani eksekusi penonaktifan server
        if ($isSuperadmin || $user->hasRole('admin_infrastruktur')) {
            $infraDeactivationQuery = Pengajuan::with(['layanan', 'instansi', 'aplikasi'])
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->where('jenis_pengajuan', 'app_deactivation')
                        ->orWhereHas('layanan', fn ($l) => $l->where('slug', 'penonaktifan-aplikasi'));
                })
                ->whereHas('aplikasi', fn ($a) => $a->where('status', '!=', 'nonaktif'));

            if ($search) {
                $infraDeactivationQuery->where(function ($q) use ($search) {
                    $q->where('kode_pengajuan', 'like', "%{$search}%")
                        ->orWhere('nama_aplikasi', 'like', "%{$search}%")
                        ->orWhereHas('instansi', fn ($iq) => $iq->where('nama_instansi', 'like', "%{$search}%"));
                });
            }

            $pengajuans = $pengajuans->merge($infraDeactivationQuery->latest()->get())->unique('id');
        }

        // 2. Antrean Tahapan Proyek Berjalan (Project Lifecycle)
        $projectQuery = Project::with([
            'aplikasi.instansi',
            'pengajuan.layanan',
            'currentWorkflow.workflow.role',
            'currentWorkflow.workflow.fields',
            'tim.user',
        ])
            ->whereNotNull('current_workflow_id')
            ->where('status', '!=', 'live');

        // Jika bukan superadmin, filter proyek yang aktif di peran pengguna
        if (! $isSuperadmin) {
            $projectQuery->where(function ($q) use ($userRoleIds, $user) {
                // Sesuai role penanggung jawab tahapan aktif
                $q->whereHas('currentWorkflow.workflow', function ($wfQ) use ($userRoleIds) {
                    $wfQ->whereIn('role_id', $userRoleIds);
                });

                // Khusus Tim Pengembang: mencakup proyek di mana user terdaftar sebagai personil
                if ($user->hasRole('ketua_tim') || $user->hasRole('anggota_tim')) {
                    $q->orWhere(function ($timQ) use ($user) {
                        $timQ->whereHas('tim', fn ($t) => $t->where('user_id', $user->id))
                            ->whereIn('status', ['initiated', 'in_development']);
                    });
                }
            });
        }

        if ($search) {
            $projectQuery->where(function ($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                    ->orWhereHas('aplikasi', function ($appQ) use ($search) {
                        $appQ->where('nama_aplikasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('currentWorkflow.workflow', function ($wfQ) use ($search) {
                        $wfQ->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $projects = $projectQuery->latest('updated_at')->paginate(10)->withQueryString();

        return view('workflow.task.index', compact('projects', 'pengajuans', 'isSuperadmin', 'search'));
    }

    /**
     * Tampilkan formulir eksekusi tahapan kerja atau cerdas arahkan (smart redirect) ke menu kerja resmi terkait.
     */
    public function show(Request $request, Project $project): View|RedirectResponse
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
        $isSuperadmin = $user->hasRole('superadmin');

        // Validasi Otorisasi: Pengguna harus memiliki role yang ditugaskan atau superadmin
        if ($assignedRoleId && ! $user->roles->contains('id', $assignedRoleId) && ! $isSuperadmin) {
            $roleName = $workflow->role?->name ?? 'peran lain';
            abort(403, "Akses ditolak. Tahapan '{$workflow->nama}' ditugaskan secara khusus untuk peran '{$roleName}'.");
        }

        // SMART REDIRECT: Arahkan langsung ke halaman modul resmi jika belum meminta tampilan fallback
        if (! $request->boolean('no_redirect')) {
            $slug = $workflow->slug;

            // 1. Tahap Uji Fungsional -> Arahkan ke Form Uji Fungsional APTIKA
            if (in_array($slug, ['functional_testing', 'functional-testing']) && ($user->hasRole('admin_aptika') || $isSuperadmin)) {
                return redirect()->route('aptika.testing.create', ['project_id' => $project->id])
                    ->with('info', "Anda diarahkan langsung ke halaman resmi Uji Fungsional untuk memproses proyek '{$project->nama_project}'.");
            }

            // 2. Tahap Uji Keamanan Siber -> Arahkan ke Form Audit Keamanan CSIRT Persandian
            if (in_array($slug, ['security_testing', 'security-testing']) && ($user->hasRole('admin_persandian') || $isSuperadmin)) {
                return redirect()->route('persandian.pengujian.create', $project)
                    ->with('info', "Anda diarahkan langsung ke halaman resmi Audit Keamanan Informasi CSIRT untuk proyek '{$project->nama_project}'.");
            }

            // 3. Tahap Deployment Hosting -> Arahkan ke Form Deployment Infrastruktur
            if (in_array($slug, ['deployment', 'hosting']) && ($user->hasRole('admin_infrastruktur') || $isSuperadmin)) {
                return redirect()->route('infrastruktur.deployment.create', $project)
                    ->with('info', "Anda diarahkan langsung ke halaman resmi Alokasi Server Hosting & Deployment untuk proyek '{$project->nama_project}'.");
            }

            // 4. Tahap Pengerjaan Tim -> Arahkan ke Lembar Kerja Proyek Tim
            if (in_array($slug, ['in_development', 'initiated', 'pengembangan', 'pengerjaan']) && ($user->hasRole('ketua_tim') || $user->hasRole('anggota_tim') || $isSuperadmin)) {
                return redirect()->route('tim.project.show', $project)
                    ->with('info', "Anda diarahkan langsung ke Lembar Kerja Proyek Tim untuk proyek '{$project->nama_project}'.");
            }

            // 5. Tahap Decommissioning -> Arahkan ke Antrean Penonaktifan Infrastruktur
            if ($slug === 'decommissioning' && ($user->hasRole('admin_infrastruktur') || $isSuperadmin)) {
                return redirect()->route('infrastruktur.penonaktifan.index')
                    ->with('info', 'Anda diarahkan langsung ke Antrean Penonaktifan Server & Sistem.');
            }
        }

        $workflowData = $project->pengajuan?->workflowData
            ->firstWhere('layanan_workflow_id', $project->current_workflow_id);

        $dedicatedMeta = self::resolveTaskMeta($project);

        return view('workflow.task.show', compact('project', 'workflow', 'workflowData', 'dedicatedMeta'));
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
            $validated['catatan_revisi']
        );

        return redirect()->route('workflow.task.index')
            ->with('warning', "Proyek '{$project->nama_project}' telah dikembalikan ke Tim Pengembang untuk perbaikan.");
    }

    /**
     * Resolves the target route, action button label, icon, and menu name for any workflow task.
     *
     * @return array{url: string, label: string, menu: string, icon: string, color: string}
     */
    public static function resolveTaskMeta(Project|Pengajuan $item): array
    {
        if ($item instanceof Pengajuan) {
            // Pengajuan Disetujui yang butuh inisiasi proyek (APTIKA)
            if ($item->status === 'approved' && ! $item->project && in_array($item->jenis_pengajuan, ['new_app', 'app_development'])) {
                return [
                    'url' => route('aptika.project.create', ['pengajuan_id' => $item->id]),
                    'label' => 'Inisiasi Proyek Baru',
                    'menu' => 'Manajemen Proyek APTIKA',
                    'icon' => 'assignment_add',
                    'color' => 'emerald',
                ];
            }

            // Pengajuan Penonaktifan yang menunggu decommissioning (Infrastruktur)
            if ($item->status === 'approved' && ($item->jenis_pengajuan === 'app_deactivation' || $item->layanan?->slug === 'penonaktifan-aplikasi')) {
                return [
                    'url' => route('infrastruktur.penonaktifan.index'),
                    'label' => 'Eksekusi Penonaktifan Server',
                    'menu' => 'Penonaktifan Aplikasi',
                    'icon' => 'power_settings_new',
                    'color' => 'amber',
                ];
            }

            // Pengajuan baru / dalam telaah teknis (APTIKA)
            return [
                'url' => route('aptika.pengajuan.show', $item),
                'label' => 'Buka Telaah Berkas',
                'menu' => 'Telaah Pengajuan Usulan',
                'icon' => 'rate_review',
                'color' => 'blue',
            ];
        }

        // Untuk entitas Project:
        $slug = $item->currentWorkflow?->workflow?->slug ?? $item->status;

        return match ($slug) {
            'functional_testing', 'functional-testing' => [
                'url' => route('aptika.testing.create', ['project_id' => $item->id]),
                'label' => 'Lakukan Uji Fungsional',
                'menu' => 'Kendali Mutu & Uji Fungsional',
                'icon' => 'fact_check',
                'color' => 'amber',
            ],
            'security_testing', 'security-testing' => [
                'url' => route('persandian.pengujian.create', $item),
                'label' => 'Lakukan Audit Keamanan',
                'menu' => 'Audit Keamanan Informasi (CSIRT)',
                'icon' => 'security',
                'color' => 'rose',
            ],
            'deployment', 'hosting' => [
                'url' => route('infrastruktur.deployment.create', $item),
                'label' => 'Eksekusi Deployment Hosting',
                'menu' => 'Operasional Deployment',
                'icon' => 'rocket_launch',
                'color' => 'cyan',
            ],
            'in_development', 'initiated', 'pengembangan', 'pengerjaan' => [
                'url' => route('tim.project.show', $item),
                'label' => 'Buka Lembar Kerja Tim',
                'menu' => 'Proyek Tim Pengembang',
                'icon' => 'engineering',
                'color' => 'indigo',
            ],
            'decommissioning' => [
                'url' => route('infrastruktur.penonaktifan.index'),
                'label' => 'Eksekusi Penonaktifan',
                'menu' => 'Penonaktifan Aplikasi',
                'icon' => 'power_settings_new',
                'color' => 'amber',
            ],
            default => [
                'url' => route('workflow.task.show', $item),
                'label' => 'Proses Tahapan',
                'menu' => 'Antrean Alur Universal',
                'icon' => 'task_alt',
                'color' => 'blue',
            ],
        };
    }
}
