<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Models\Aplikasi;
use App\Models\DokumenPerancangan;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\TimProject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Tampilkan daftar seluruh proyek pengembangan aplikasi.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $projects = Project::with(['aplikasi.instansi', 'pengajuan', 'tim.user'])
            ->when($search, function ($query, $search) {
                $query->where('nama_project', 'like', "%{$search}%")
                    ->orWhereHas('aplikasi', function ($q) use ($search) {
                        $q->where('nama_aplikasi', 'like', "%{$search}%")
                            ->orWhereHas('instansi', function ($iq) use ($search) {
                                $iq->where('nama_instansi', 'like', "%{$search}%");
                            });
                    });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('aptika.project.index', compact('projects', 'search', 'status'));
    }

    /**
     * Formulir inisiasi proyek baru dari permohonan yang telah disetujui.
     */
    public function create(Request $request): View
    {
        $selectedPengajuanId = $request->query('pengajuan_id');

        // Pengajuan disetujui yang belum memiliki proyek
        $pengajuans = Pengajuan::where('status', 'approved')
            ->whereDoesntHave('project')
            ->with(['instansi', 'aplikasi'])
            ->latest()
            ->get();

        $selectedPengajuan = null;
        if ($selectedPengajuanId) {
            $selectedPengajuan = Pengajuan::with(['instansi', 'aplikasi'])->find($selectedPengajuanId);
        }

        $developers = User::role(['ketua_tim', 'anggota_tim'])->orderBy('name')->get();
        $ketuaTims = $developers;
        $anggotaTims = $developers;

        return view('aptika.project.create', compact('pengajuans', 'selectedPengajuan', 'developers', 'ketuaTims', 'anggotaTims'));
    }

    /**
     * Simpan inisiasi proyek dan susunan tim pengembang.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pengajuan_id' => ['required', 'exists:pengajuan,id'],
            'nama_project' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'ketua_tim_id' => ['required', 'exists:users,id'],
            'anggota_tim_ids' => ['nullable', 'array'],
            'anggota_tim_ids.*' => ['exists:users,id'],
        ], [
            'tanggal_selesai.after_or_equal' => 'Target tanggal selesai tidak boleh lebih awal dari tanggal mulai proyek.',
        ]);

        return DB::transaction(function () use ($request) {
            $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);

            // Pastikan aplikasi terdaftar di tabel master aplikasi
            $aplikasi = $pengajuan->aplikasi;
            if (! $aplikasi) {
                $aplikasi = Aplikasi::create([
                    'instansi_id' => $pengajuan->instansi_id,
                    'nama_aplikasi' => $pengajuan->nama_aplikasi ?? $request->nama_project,
                    'deskripsi' => $pengajuan->deskripsi_kebutuhan,
                    'status' => 'pengembangan',
                ]);
                $pengajuan->update(['aplikasi_id' => $aplikasi->id]);
            } else {
                $aplikasi->update(['status' => 'pengembangan']);
            }

            // Buat Project
            $project = Project::create([
                'pengajuan_id' => $pengajuan->id,
                'aplikasi_id' => $aplikasi->id,
                'nama_project' => $request->nama_project,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => 'initiated',
                'progres_total' => 0,
            ]);

            // Assign Ketua Tim
            TimProject::create([
                'project_id' => $project->id,
                'user_id' => $request->ketua_tim_id,
                'peran' => 'ketua_tim',
            ]);

            // Assign Anggota Tim
            $members = $request->input('anggota_tim_ids', []);
            foreach ($members as $memberId) {
                if ($memberId != $request->ketua_tim_id) {
                    TimProject::create([
                        'project_id' => $project->id,
                        'user_id' => $memberId,
                        'peran' => 'anggota',
                    ]);
                }
            }

            return redirect()->route('aptika.project.show', $project)
                ->with('success', "Proyek '{$project->nama_project}' berhasil diinisiasi dengan tim pengembang yang telah ditentukan.");
        });
    }

    /**
     * Tampilkan detail proyek, susunan tim, pengujian, dokumen perancangan, dan riwayat rapat.
     */
    public function show(Project $project): View
    {
        $project->load([
            'aplikasi.instansi',
            'pengajuan.dokumen.masterDokumen',
            'tim.user',
            'dokumenPerancangan' => function ($q) {
                $q->with('penilai')->latest();
            },
            'testingAplikasi.penguji',
            'pengujianKeamanan.tester',
            'rapat.peserta.user',
            'hostingAplikasi',
        ]);

        $developers = User::role(['ketua_tim', 'anggota_tim'])->orderBy('name')->get();

        return view('aptika.project.show', compact('project', 'developers'));
    }

    /**
     * Telaah dokumen perancangan (SRS, ERD, Wireframe, dll.) yang diunggah oleh Ketua Tim.
     */
    public function reviewDokumen(Request $request, Project $project, DokumenPerancangan $dokumen): RedirectResponse
    {
        if ($dokumen->project_id !== $project->id) {
            abort(404, 'Dokumen perancangan tidak terkait dengan proyek ini.');
        }

        $request->validate([
            'status' => ['required', 'in:disetujui,ditolak,menunggu_review'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $dokumen->update([
            'status' => $request->input('status'),
            'catatan' => $request->input('catatan'),
            'dinilai_oleh' => auth()->id(),
            'tanggal_penilaian' => now(),
        ]);

        return back()->with('success', 'Status telaah Dokumen '.strtoupper($dokumen->jenis_dokumen).' berhasil diperbarui menjadi '.str_replace('_', ' ', $dokumen->status).'.');
    }

    /**
     * Perbarui status siklus hidup proyek (misal maju ke Uji Fungsional / Keamanan).
     */
    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:initiated,in_development,functional_testing,security_testing,deployment,live,inactive,delayed'],
            'progres_total' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $data = ['status' => $request->input('status')];
        if ($request->filled('progres_total')) {
            $data['progres_total'] = $request->input('progres_total');
        }

        $project->update($data);

        return back()->with('success', "Status proyek {$project->nama_project} berhasil diubah ke: ".ucfirst(str_replace('_', ' ', $project->status)));
    }

    /**
     * Perbarui susunan tim pengembang proyek (ganti ketua tim atau ubah anggota).
     */
    public function updateTim(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'ketua_tim_id' => ['required', 'exists:users,id'],
            'anggota_tim_ids' => ['nullable', 'array'],
            'anggota_tim_ids.*' => ['exists:users,id'],
        ]);

        DB::transaction(function () use ($request, $project) {
            // Hapus penugasan tim sebelumnya untuk proyek ini
            $project->tim()->delete();

            // Simpan Ketua Tim
            TimProject::create([
                'project_id' => $project->id,
                'user_id' => $request->ketua_tim_id,
                'peran' => 'ketua_tim',
            ]);

            // Simpan Anggota Tim (kecuali jika terpilih sebagai ketua tim)
            $members = $request->input('anggota_tim_ids', []);
            foreach ($members as $memberId) {
                if ($memberId != $request->ketua_tim_id) {
                    TimProject::create([
                        'project_id' => $project->id,
                        'user_id' => $memberId,
                        'peran' => 'anggota',
                    ]);
                }
            }
        });

        return back()->with('success', "Susunan tim pengembang untuk proyek '{$project->nama_project}' berhasil diperbarui.");
    }
}
