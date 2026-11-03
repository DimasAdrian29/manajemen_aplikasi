<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\Layanan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkflowMonitoringController extends Controller
{
    /**
     * Tampilkan pemantauan terpadu alur workflow aplikasi berjalan untuk seluruh peran.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');
        $isOpd = $user->hasRole('opd');

        $search = $request->query('search');
        $layananId = $request->query('layanan_id');
        $instansiId = $request->query('instansi_id');
        $statusTahapan = $request->query('status_tahapan');

        // Base query proyek
        $baseQuery = Project::with([
            'aplikasi.instansi',
            'pengajuan.layanan.workflows.workflow.role',
            'pengajuan.instansi',
            'pengajuan.workflowData.layananWorkflow.workflow',
            'currentWorkflow.workflow.role',
            'tim.user',
        ]);

        // Scoping data: User OPD hanya melihat aplikasi instansinya sendiri
        if ($isOpd && ! $isSuperadmin) {
            $baseQuery->where(function ($q) use ($user) {
                $q->whereHas('pengajuan', fn ($pq) => $pq->where('instansi_id', $user->instansi_id))
                    ->orWhereHas('aplikasi', fn ($aq) => $aq->where('instansi_id', $user->instansi_id));
            });
        } elseif ($instansiId && ! $isOpd) {
            $baseQuery->where(function ($q) use ($instansiId) {
                $q->whereHas('pengajuan', fn ($pq) => $pq->where('instansi_id', $instansiId))
                    ->orWhereHas('aplikasi', fn ($aq) => $aq->where('instansi_id', $instansiId));
            });
        }

        // Kalkulasi ringkasan statistik sebelum filter spesifik
        $statsQuery = clone $baseQuery;
        $stats = [
            'total_berjalan' => (clone $statsQuery)->where('status', '!=', 'inactive')->count(),
            'in_development' => (clone $statsQuery)->whereIn('status', ['initiated', 'in_development'])->count(),
            'functional_testing' => (clone $statsQuery)->where('status', 'functional_testing')->count(),
            'security_testing' => (clone $statsQuery)->where('status', 'security_testing')->count(),
            'deployment' => (clone $statsQuery)->where('status', 'deployment')->count(),
            'live' => (clone $statsQuery)->where('status', 'live')->count(),
        ];

        // Filter pencarian
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                    ->orWhereHas('aplikasi', function ($appQ) use ($search) {
                        $appQ->where('nama_aplikasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pengajuan', function ($pengQ) use ($search) {
                        $pengQ->where('kode_pengajuan', 'like', "%{$search}%")
                            ->orWhereHas('instansi', function ($instQ) use ($search) {
                                $instQ->where('nama_instansi', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('currentWorkflow.workflow', function ($wfQ) use ($search) {
                        $wfQ->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Filter jenis layanan
        if ($layananId) {
            $baseQuery->whereHas('pengajuan', function ($q) use ($layananId) {
                $q->where('layanan_id', $layananId);
            });
        }

        // Filter status tahapan spesifik
        if ($statusTahapan && $statusTahapan !== 'all') {
            if ($statusTahapan === 'in_development') {
                $baseQuery->whereIn('status', ['initiated', 'in_development']);
            } else {
                $baseQuery->where('status', $statusTahapan);
            }
        }

        $projects = $baseQuery->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        $layanans = Layanan::where('is_active', true)->orderBy('urutan')->get();
        $instansis = ! $isOpd ? Instansi::orderBy('nama_instansi')->get() : collect();

        return view('workflow.monitoring.index', compact(
            'projects',
            'stats',
            'layanans',
            'instansis',
            'isSuperadmin',
            'isOpd',
            'search',
            'layananId',
            'instansiId',
            'statusTahapan'
        ));
    }

    /**
     * Tampilkan detail pelacakan alur workflow, riwayat tahapan, dan dokumen pendukung suatu proyek.
     */
    public function show(Project $project): View
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');
        $isOpd = $user->hasRole('opd');

        // Scoping proteksi: OPD dilarang melihat proyek instansi lain
        if ($isOpd && ! $isSuperadmin) {
            $projectInstansiId = $project->pengajuan?->instansi_id ?? $project->aplikasi?->instansi_id;
            abort_if($projectInstansiId !== $user->instansi_id, 403, 'Anda tidak memiliki hak akses untuk memantau proyek instansi lain.');
        }

        $project->loadMissing([
            'aplikasi.instansi',
            'pengajuan.layanan.workflows.workflow.role',
            'pengajuan.instansi',
            'pengajuan.dokumen.masterDokumen',
            'pengajuan.workflowData.layananWorkflow.workflow.role',
            'pengajuan.workflowData.completedBy',
            'pengajuan.workflowData.fieldData.workflowField',
            'currentWorkflow.workflow.role',
            'tim.user',
            'dokumenPerancangan',
            'pengujianKeamanan',
            'testingAplikasi',
            'hostingAplikasi',
            'tahapan',
        ]);

        return view('workflow.monitoring.show', compact('project', 'isOpd', 'isSuperadmin'));
    }
}
