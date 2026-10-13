<?php

namespace App\Services\Workflow;

use App\Models\LayananWorkflow;
use App\Models\Pengajuan;
use App\Models\PengajuanWorkflowData;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WorkflowTransitionService
{
    public function __construct(
        protected WorkflowFieldService $fieldService
    ) {}

    /**
     * Majukan proyek ke tahapan aktif berikutnya berdasarkan pipeline layanan.
     *
     * @param  array{fields?: array<int|string, mixed>, files?: array<int|string, mixed>}  $inputData
     */
    public function advance(Project $project, User $actor, ?string $catatan = null, array $inputData = []): ?LayananWorkflow
    {
        return DB::transaction(function () use ($project, $actor, $catatan, $inputData) {
            $project->loadMissing(['pengajuan.layanan', 'currentWorkflow.workflow', 'aplikasi']);

            $pengajuan = $project->pengajuan;
            $currentLayananWorkflow = $project->currentWorkflow;
            $layananId = $pengajuan?->layanan_id ?? $currentLayananWorkflow?->layanan_id;

            // 1. Selesaikan tahapan saat ini pada tabel riwayat stepper
            if ($currentLayananWorkflow && $pengajuan) {
                $workflowData = PengajuanWorkflowData::firstOrCreate(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'layanan_workflow_id' => $currentLayananWorkflow->id,
                    ],
                    [
                        'status' => 'in_progress',
                    ]
                );

                $workflowData->update([
                    'status' => 'completed',
                    'completed_by' => $actor->id,
                    'completed_at' => now(),
                    'catatan' => $catatan ?? $workflowData->catatan,
                ]);

                // Simpan field dinamis jika tersedia
                if (! empty($inputData)) {
                    $this->fieldService->saveFieldValues($workflowData, $inputData);
                }
            }

            // 2. Cari tahapan aktif berikutnya
            $currentUrutan = $currentLayananWorkflow?->urutan ?? 0;
            $nextLayananWorkflow = null;

            if ($layananId) {
                // Jika belum punya currentLayananWorkflow, cocokkan dengan status proyek saat ini
                if (! $currentLayananWorkflow) {
                    $currentLayananWorkflow = LayananWorkflow::where('layanan_id', $layananId)
                        ->whereHas('workflow', function ($q) use ($project) {
                            $q->where('slug', $project->status)
                                ->orWhere('slug', str_replace('_', '-', $project->status))
                                ->orWhere('slug', str_replace('-', '_', $project->status));
                        })->first();
                    $currentUrutan = $currentLayananWorkflow?->urutan ?? 0;
                }

                $nextLayananWorkflow = LayananWorkflow::with('workflow')
                    ->where('layanan_id', $layananId)
                    ->where('is_active', true)
                    ->where('urutan', '>', $currentUrutan)
                    ->orderBy('urutan', 'asc')
                    ->first();
            } else {
                // Fallback untuk data legacy / unit test tanpa relasi Layanan eksplisit
                $legacyNextStatus = match ($project->status) {
                    'initiated', 'in_development' => 'functional_testing',
                    'functional_testing' => 'security_testing',
                    'security_testing' => 'deployment',
                    'deployment' => 'live',
                    default => 'live',
                };

                $project->update([
                    'status' => $legacyNextStatus,
                    'progres_total' => match ($legacyNextStatus) {
                        'functional_testing' => max((int) $project->progres_total, 60),
                        'security_testing' => max((int) $project->progres_total, 75),
                        'deployment' => max((int) $project->progres_total, 90),
                        'live' => 100,
                        default => $project->progres_total,
                    },
                ]);

                if ($legacyNextStatus === 'live' && $project->aplikasi) {
                    $project->aplikasi->update(['status' => 'aktif']);
                }

                return null;
            }

            // 3. Hitung persentase progres dinamis
            $totalSteps = LayananWorkflow::where('layanan_id', $layananId)->where('is_active', true)->count();
            $completedSteps = $pengajuan ? PengajuanWorkflowData::where('pengajuan_id', $pengajuan->id)->where('status', 'completed')->count() : 1;
            $progresPersen = $totalSteps > 0 ? (int) round(($completedSteps / $totalSteps) * 100) : 100;

            // 4. Transisi ke step berikutnya atau tandai rampung (LIVE)
            if ($nextLayananWorkflow) {
                $targetStatus = $nextLayananWorkflow->workflow->target_project_status;

                $project->update([
                    'current_workflow_id' => $nextLayananWorkflow->id,
                    'status' => $targetStatus,
                    'progres_total' => max((int) $project->progres_total, $progresPersen),
                ]);

                if ($pengajuan) {
                    PengajuanWorkflowData::firstOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'layanan_workflow_id' => $nextLayananWorkflow->id,
                        ],
                        [
                            'status' => 'in_progress',
                        ]
                    );
                }

                return $nextLayananWorkflow;
            }

            // Jika tidak ada step berikutnya, proyek telah selesai seluruh siklus
            $project->update([
                'status' => 'live',
                'progres_total' => 100,
            ]);

            if ($project->aplikasi) {
                $project->aplikasi->update([
                    'status' => 'aktif',
                ]);
            }

            return null;
        });
    }

    /**
     * Kembalikan proyek ke tahap revisi perbaikan (misal bug fungsional atau temuan audit keamanan).
     */
    public function sendForRevision(Project $project, User $actor, string $catatan, string $targetStatus = 'in_development'): void
    {
        DB::transaction(function () use ($project, $actor, $catatan, $targetStatus) {
            $project->loadMissing(['pengajuan', 'currentWorkflow']);

            $project->update([
                'status' => $targetStatus,
            ]);

            if ($project->current_workflow_id && $project->pengajuan_id) {
                PengajuanWorkflowData::updateOrCreate(
                    [
                        'pengajuan_id' => $project->pengajuan_id,
                        'layanan_workflow_id' => $project->current_workflow_id,
                    ],
                    [
                        'status' => 'rejected',
                        'catatan' => $catatan,
                        'completed_by' => $actor->id,
                    ]
                );
            }
        });
    }

    /**
     * Inisialisasi daftar tahapan workflow untuk usulan pengajuan baru.
     */
    public function initializeForPengajuan(Pengajuan $pengajuan): void
    {
        if (! $pengajuan->layanan_id) {
            return;
        }

        $workflows = LayananWorkflow::where('layanan_id', $pengajuan->layanan_id)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        foreach ($workflows as $index => $lw) {
            PengajuanWorkflowData::firstOrCreate(
                [
                    'pengajuan_id' => $pengajuan->id,
                    'layanan_workflow_id' => $lw->id,
                ],
                [
                    'status' => $index === 0 ? 'in_progress' : 'pending',
                ]
            );
        }
    }
}
