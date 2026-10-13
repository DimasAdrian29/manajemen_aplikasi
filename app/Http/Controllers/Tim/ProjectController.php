<?php

namespace App\Http\Controllers\Tim;

use App\Http\Controllers\Controller;
use App\Models\DokumenPerancangan;
use App\Models\Penundaan;
use App\Models\Project;
use App\Models\TahapanProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Tampilkan daftar seluruh proyek yang ditugaskan kepada user.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $tab = $request->query('tab', 'semua');
        $search = $request->query('search');

        $projectsQuery = Project::whereHas('tim', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['aplikasi.instansi', 'tim.user', 'tahapan'])
            ->when($search, function ($query, $search) {
                $query->where('nama_project', 'like', "%{$search}%")
                    ->orWhereHas('aplikasi', function ($q) use ($search) {
                        $q->where('nama_aplikasi', 'like', "%{$search}%");
                    });
            });

        if ($tab === 'ketua') {
            $projectsQuery->whereHas('tim', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('peran', 'ketua_tim');
            });
        } elseif ($tab === 'anggota') {
            $projectsQuery->whereHas('tim', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('peran', 'anggota');
            });
        }

        $projects = $projectsQuery->latest()->paginate(9)->withQueryString();

        return view('tim.project.index', compact('projects', 'tab', 'search'));
    }

    /**
     * Tampilkan lembar kerja proyek (menyesuaikan wewenang Ketua vs Anggota).
     */
    public function show(Project $project): View
    {
        $user = auth()->user();

        // Pastikan pengguna terdaftar di dalam tim proyek ini
        $timAssignment = $project->tim()->where('user_id', $user->id)->first();
        if (! $timAssignment) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai personil pengembang pada proyek ini.');
        }

        $isLead = ($timAssignment->peran === 'ketua_tim');

        $project->load([
            'aplikasi.instansi',
            'pengajuan.dokumen.masterDokumen',
            'tim.user',
            'tahapan' => function ($q) {
                $q->orderBy('urutan');
            },
            'dokumenPerancangan' => function ($q) {
                $q->latest();
            },
            'penundaan' => function ($q) {
                $q->latest();
            },
            'testingAplikasi.penguji',
            'pengujianKeamanan.tester',
            'rapat' => function ($q) {
                $q->latest()->take(5);
            },
            'hostingAplikasi',
        ]);

        return view('tim.project.show', compact('project', 'isLead'));
    }

    /**
     * Update progres proyek dan tahapan sprint (Khusus Ketua Tim).
     */
    public function updateProgres(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeLead($project);

        $request->validate([
            'progres_total' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['nullable', 'string', 'max:100'],
            'tahapan' => ['nullable', 'array'],
            'tahapan.*.id' => ['required', 'exists:tahapan_project,id'],
            'tahapan.*.persentase' => ['required', 'integer', 'min:0', 'max:100'],
            'tahapan.*.status' => ['required', 'in:belum_mulai,dalam_pengerjaan,selesai'],
        ]);

        DB::transaction(function () use ($request, $project) {
            // Update progres total proyek
            $updateData = ['progres_total' => $request->input('progres_total')];
            if ($request->filled('status')) {
                $statusVal = $request->input('status');
                $updateData['status'] = $statusVal;

                $lw = $project->pengajuan?->layanan?->workflows()
                    ->whereHas('workflow', function ($q) use ($statusVal) {
                        $q->where('slug', $statusVal)
                            ->orWhere('slug', str_replace('_', '-', $statusVal))
                            ->orWhere('slug', str_replace('-', '_', $statusVal));
                    })->first();

                if ($lw) {
                    $updateData['current_workflow_id'] = $lw->id;
                    $project->pengajuan->workflowData()
                        ->where('layanan_workflow_id', $lw->id)
                        ->update(['status' => 'in_progress']);

                    // Selesaikan step-step sebelumnya yang telah terlewati
                    $project->pengajuan->workflowData()
                        ->whereHas('layananWorkflow', function ($q) use ($lw) {
                            $q->where('urutan', '<', $lw->urutan);
                        })
                        ->where('status', '!=', 'completed')
                        ->update([
                            'status' => 'completed',
                            'completed_by' => auth()->id(),
                            'completed_at' => now(),
                        ]);
                }
            }
            $project->update($updateData);

            // Update masing-masing tahapan sprint
            if ($request->has('tahapan')) {
                foreach ($request->input('tahapan') as $tahapData) {
                    TahapanProject::where('id', $tahapData['id'])
                        ->where('project_id', $project->id)
                        ->update([
                            'persentase' => $tahapData['persentase'],
                            'status' => $tahapData['status'],
                        ]);
                }
            }
        });

        return back()->with('success', 'Progres sprint dan capaian proyek berhasil diperbarui.');
    }

    /**
     * Tambah tahapan sprint baru (Khusus Ketua Tim).
     */
    public function storeTahapan(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeLead($project);

        $request->validate([
            'nama_tahapan' => ['required', 'string', 'max:255'],
            'persentase' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $nextUrutan = ($project->tahapan()->max('urutan') ?? 0) + 1;

        TahapanProject::create([
            'project_id' => $project->id,
            'nama_tahapan' => $request->input('nama_tahapan'),
            'urutan' => $nextUrutan,
            'persentase' => $request->input('persentase', 0),
            'status' => $request->input('persentase', 0) >= 100 ? 'selesai' : ($request->input('persentase', 0) > 0 ? 'dalam_pengerjaan' : 'belum_mulai'),
        ]);

        return back()->with('success', "Tahapan sprint '{$request->nama_tahapan}' berhasil ditambahkan.");
    }

    /**
     * Upload berkas perancangan atau tautkan link Google Docs/Figma (Khusus Ketua Tim).
     */
    public function storeDokumen(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeLead($project);

        $request->validate([
            'jenis_dokumen' => ['required', 'in:srs,wireframe,erd,arsitektur,lainnya'],
            'tipe_sumber' => ['required', 'in:file,link'],
            'file_dokumen' => ['required_if:tipe_sumber,file', 'nullable', 'file', 'mimes:pdf,zip,doc,docx', 'max:20480'],
            'link_dokumen' => ['required_if:tipe_sumber,link', 'nullable', 'url', 'max:500'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'file_dokumen.required_if' => 'Silakan pilih berkas dokumen yang ingin diunggah.',
            'link_dokumen.required_if' => 'Silakan masukkan URL tautan dokumen (Google Docs / Figma / Notion).',
            'link_dokumen.url' => 'Format tautan tidak valid. Pastikan menyertakan http:// atau https://',
        ]);

        $dokumenPath = null;
        if ($request->input('tipe_sumber') === 'link') {
            $dokumenPath = $request->input('link_dokumen');
        } else {
            if ($request->hasFile('file_dokumen')) {
                $dokumenPath = $request->file('file_dokumen')->store('perancangan', 'public');
            }
        }

        DokumenPerancangan::create([
            'project_id' => $project->id,
            'jenis_dokumen' => $request->input('jenis_dokumen'),
            'file_dokumen' => $dokumenPath,
            'status' => 'menunggu_review',
            'catatan' => $request->input('catatan'),
        ]);

        return back()->with('success', 'Dokumen perancangan berhasil disimpan dan diajukan untuk penelaahan APTIKA.');
    }

    /**
     * Ajukan permohonan penundaan proyek jika ada kendala teknis (Khusus Ketua Tim).
     */
    public function storePenundaan(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeLead($project);

        $request->validate([
            'alasan' => ['required', 'string', 'min:10', 'max:2000'],
            'file_surat' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('file_surat')) {
            $filePath = $request->file('file_surat')->store('penundaan', 'public');
        }

        DB::transaction(function () use ($request, $project, $filePath) {
            Penundaan::create([
                'project_id' => $project->id,
                'diajukan_oleh' => auth()->id(),
                'alasan' => $request->input('alasan'),
                'file_surat' => $filePath,
                'status' => 'diajukan',
            ]);

            // Set status proyek menjadi delayed
            $project->update(['status' => 'delayed']);
        });

        return back()->with('success', 'Permohonan penundaan proyek berhasil diajukan ke Bidang APTIKA.');
    }

    /**
     * Pastikan pengguna saat ini adalah Ketua Tim pada proyek tersebut.
     */
    private function authorizeLead(Project $project): void
    {
        $userId = auth()->id();
        $isLead = $project->tim()->where('user_id', $userId)->where('peran', 'ketua_tim')->exists();

        if (! $isLead) {
            abort(403, 'Akses Ditolak: Tindakan ini hanya dapat dilakukan oleh Ketua Tim pengembang pada proyek ini.');
        }
    }
}
