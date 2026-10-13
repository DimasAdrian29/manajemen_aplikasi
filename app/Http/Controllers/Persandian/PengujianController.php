<?php

namespace App\Http\Controllers\Persandian;

use App\Http\Controllers\Controller;
use App\Http\Requests\Persandian\PengujianKeamananRequest;
use App\Models\PengujianKeamanan;
use App\Models\Project;
use App\Notifications\HasilUjiKeamananNotification;
use App\Services\Workflow\WorkflowTransitionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengujianController extends Controller
{
    /**
     * Display projects in security testing queue.
     */
    public function index(Request $request): View
    {
        $query = Project::with([
            'aplikasi.instansi',
            'pengajuan',
            'tim.user',
            'testingAplikasi',
            'pengujianKeamanan',
        ])->where('status', 'security_testing');

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

        return view('persandian.pengujian.index', compact('projects'));
    }

    public function __construct(
        protected WorkflowTransitionService $workflowService
    ) {}

    /**
     * Show the form for creating a new security audit.
     */
    public function create(Project $project): View|RedirectResponse
    {
        if ($project->status !== 'security_testing') {
            return redirect()
                ->route('persandian.pengujian.index')
                ->with('error', "Proyek '{$project->nama_project}' belum berada dalam tahap uji keamanan informasi.");
        }

        $project->load([
            'aplikasi.instansi',
            'pengajuan',
            'tim.user',
            'testingAplikasi',
            'pengujianKeamanan.tester',
            'currentWorkflow.workflow.fields',
        ]);

        return view('persandian.pengujian.create', compact('project'));
    }

    /**
     * Store security audit result and perform automated status transitions.
     */
    public function store(PengujianKeamananRequest $request, Project $project): RedirectResponse
    {
        if ($project->status !== 'security_testing') {
            return redirect()
                ->route('persandian.pengujian.index')
                ->with('error', "Proyek '{$project->nama_project}' tidak dapat diaudit karena statusnya bukan security testing.");
        }

        $validated = $request->validated();

        $filePath = null;
        if ($request->hasFile('file_hasil')) {
            $file = $request->file('file_hasil');
            $filename = 'audit_keamanan_'.time().'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs("security_testing/project_{$project->id}", $filename, 'public');
        }

        $nextWorkflow = null;

        $pengujian = DB::transaction(function () use ($project, $validated, $filePath, $request, &$nextWorkflow) {
            $pengujian = PengujianKeamanan::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'kategori_pengujian' => $validated['kategori_pengujian'],
                'hasil' => $validated['hasil'],
                'tingkat_risiko' => $validated['tingkat_risiko'],
                'tanggal_uji' => $validated['tanggal_uji'],
                'nomor_surat' => $validated['nomor_surat'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'catatan_kerentanan' => $validated['catatan_kerentanan'] ?? null,
                'rekomendasi' => $validated['rekomendasi'] ?? null,
                'file_hasil' => $filePath,
            ]);

            // Status transitions based on security audit result using WorkflowTransitionService
            if ($validated['hasil'] === 'lulus') {
                $dynamicInput = [
                    'fields' => $request->input('workflow_fields', []),
                    'files' => $request->file('workflow_files', []),
                ];

                $nextWorkflow = $this->workflowService->advance(
                    $project,
                    auth()->user(),
                    $validated['catatan_kerentanan'] ?: 'Audit Keamanan LULUS.',
                    $dynamicInput
                );
            } elseif ($validated['hasil'] === 'perbaikan') {
                $this->workflowService->sendForRevision(
                    $project,
                    auth()->user(),
                    $validated['catatan_kerentanan'] ?: 'Temuan kerentanan perlu mitigasi perbaikan.'
                );
            } else {
                $this->workflowService->sendForRevision(
                    $project,
                    auth()->user(),
                    $validated['catatan_kerentanan'] ?: 'Audit keamanan tidak lulus.'
                );
            }

            // Update pengajuan audit log
            if ($project->pengajuan) {
                $statusText = strtoupper($validated['hasil']);
                $riskText = strtoupper($validated['tingkat_risiko']);
                $skloText = ! empty($validated['nomor_surat']) ? " SKLO: {$validated['nomor_surat']}." : '';
                $project->pengajuan->update([
                    'catatan_penilaian' => ($project->pengajuan->catatan_penilaian ? $project->pengajuan->catatan_penilaian."\n" : '').
                        '[Persandian/CSIRT '.now()->format('d/m/Y H:i')."] Hasil Uji Keamanan: {$statusText} (Risiko: {$riskText}).".
                        $skloText,
                ]);
            }

            // Send notification to Ketua Tim
            $ketuaTim = $project->tim()->where('peran', 'ketua_tim')->first()?->user;
            if ($ketuaTim) {
                $ketuaTim->notify(new HasilUjiKeamananNotification($pengujian));
            }

            // Send notification to Reviewer APTIKA
            if ($project->pengajuan?->penilai && $project->pengajuan->penilai->id !== $ketuaTim?->id) {
                $project->pengajuan->penilai->notify(new HasilUjiKeamananNotification($pengujian));
            }

            return $pengujian;
        });

        $msg = match ($validated['hasil']) {
            'lulus' => $nextWorkflow
                ? "Audit Keamanan LULUS! Proyek '{$project->nama_project}' otomatis diteruskan ke tahap {$nextWorkflow->workflow->nama}."
                : "Audit Keamanan LULUS! Seluruh tahapan alur proyek '{$project->nama_project}' telah selesai (LIVE).",
            'perbaikan' => "Temuan kerentanan dicatat. Proyek '{$project->nama_project}' dikembalikan ke Tim Pengembang untuk mitigasi perbaikan.",
            default => "Hasil audit tidak lulus telah dicatat untuk proyek '{$project->nama_project}'.",
        };

        return redirect()
            ->route('persandian.pengujian.show', $pengujian)
            ->with('success', $msg);
    }

    /**
     * Display detail of a security audit sheet.
     */
    public function show(PengujianKeamanan $pengujian): View
    {
        $pengujian->load([
            'project.aplikasi.instansi',
            'project.tim.user',
            'tester',
        ]);

        return view('persandian.pengujian.show', compact('pengujian'));
    }

    /**
     * Display all historical security audits archive.
     */
    public function riwayat(Request $request): View
    {
        $query = PengujianKeamanan::with([
            'project.aplikasi.instansi',
            'tester',
        ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('catatan_kerentanan', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pQ) use ($search) {
                        $pQ->where('nama_project', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project.aplikasi', function ($appQ) use ($search) {
                        $appQ->where('nama_aplikasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project.aplikasi.instansi', function ($instQ) use ($search) {
                        $instQ->where('nama_instansi', 'like', "%{$search}%");
                    });
            });
        }

        if ($hasil = $request->input('hasil')) {
            $query->where('hasil', $hasil);
        }

        if ($risiko = $request->input('tingkat_risiko')) {
            $query->where('tingkat_risiko', $risiko);
        }

        if ($kategori = $request->input('kategori_pengujian')) {
            $query->where('kategori_pengujian', $kategori);
        }

        $audits = $query->latest('tanggal_uji')->paginate(10)->withQueryString();

        return view('persandian.pengujian.riwayat', compact('audits'));
    }
}
