<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Models\PesertaRapat;
use App\Models\Project;
use App\Models\RapatProject;
use App\Models\User;
use App\Notifications\UndanganRapatNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RapatController extends Controller
{
    /**
     * Tampilkan agenda rapat seluruh proyek.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $projectId = $request->query('project_id');

        $rapats = RapatProject::with(['project.aplikasi.instansi', 'pembuat', 'peserta.user'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($projectId, function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })
            ->latest('waktu_rapat')
            ->paginate(10)
            ->withQueryString();

        $projects = Project::with('aplikasi')->orderBy('nama_project')->get();

        return view('aptika.rapat.index', compact('rapats', 'projects', 'status', 'projectId'));
    }

    /**
     * Form penjadwalan rapat baru untuk proyek.
     */
    public function create(Request $request): View
    {
        $selectedProjectId = $request->query('project_id');
        $projects = Project::with(['aplikasi.instansi', 'tim.user'])->get();

        $selectedProject = null;
        $potentialParticipants = collect();

        if ($selectedProjectId) {
            $selectedProject = Project::with(['aplikasi.instansi.users', 'tim.user'])->find($selectedProjectId);
            if ($selectedProject) {
                // 1. Anggota tim internal
                $teamUsers = $selectedProject->tim->pluck('user');
                // 2. User OPD dari instansi pemohon
                $opdUsers = $selectedProject->pengajuan?->instansi?->users ?? collect();
                // Gabungkan tanpa duplikasi
                $potentialParticipants = $teamUsers->merge($opdUsers)->unique('id');
            }
        }

        // Semua user yang relevan jika project belum dipilih secara spesifik
        $allUsers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['ketua_tim', 'anggota_tim', 'opd', 'admin_aptika', 'kepala_bidang']);
        })->with('instansi')->orderBy('name')->get();

        return view('aptika.rapat.create', compact('projects', 'selectedProject', 'potentialParticipants', 'allUsers'));
    }

    /**
     * Simpan jadwal rapat baru, undang peserta, dan kirimkan notifikasi sistem.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'project_id' => ['required', 'exists:project,id'],
            'nama_rapat' => ['required', 'string', 'max:255'],
            'waktu_rapat' => ['required', 'date'],
            'tempat_rapat' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'file_surat_rapat' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'peserta_ids' => ['required', 'array', 'min:1'],
            'peserta_ids.*' => ['exists:users,id'],
        ], [
            'peserta_ids.required' => 'Pilih minimal satu orang peserta rapat (anggota tim / OPD).',
        ]);

        return DB::transaction(function () use ($request) {
            $project = Project::findOrFail($request->project_id);

            $suratPath = null;
            if ($request->hasFile('file_surat_rapat')) {
                $file = $request->file('file_surat_rapat');
                $filename = 'surat_undangan_'.time().'.'.$file->getClientOriginalExtension();
                $suratPath = $file->storeAs("rapat/project_{$project->id}", $filename, 'public');
            }

            $rapat = RapatProject::create([
                'project_id' => $project->id,
                'dibuat_oleh' => auth()->id(),
                'nama_rapat' => $request->input('nama_rapat'),
                'waktu_rapat' => $request->input('waktu_rapat'),
                'tempat_rapat' => $request->input('tempat_rapat') ?? 'Ruang Rapat Bidang APTIKA Lt. 2 Diskominfo Riau',
                'deskripsi' => $request->input('deskripsi'),
                'file_surat_rapat' => $suratPath,
                'status' => 'dijadwalkan',
            ]);

            // Hubungkan peserta rapat dan kirim notifikasi otomatis
            $pesertaIds = $request->input('peserta_ids', []);
            $now = Carbon::now();

            foreach ($pesertaIds as $userId) {
                PesertaRapat::create([
                    'rapat_project_id' => $rapat->id,
                    'user_id' => $userId,
                    'status_kehadiran' => 'menunggu',
                    'notifikasi_dikirim_at' => $now,
                ]);

                // Kirim notifikasi sistem
                $user = User::find($userId);
                if ($user) {
                    $user->notify(new UndanganRapatNotification($rapat));
                }
            }

            return redirect()->route('aptika.rapat.show', $rapat)
                ->with('success', "Rapat koordinasi '{$rapat->nama_rapat}' berhasil dijadwalkan dan undangan telah dikirimkan ke ".count($pesertaIds).' peserta terhubung.');
        });
    }

    /**
     * Tampilkan detail rapat, presensi peserta, surat, serta dokumentasi/notulensi.
     */
    public function show(RapatProject $rapat): View
    {
        $rapat->load([
            'project.aplikasi.instansi',
            'pembuat',
            'peserta.user.instansi',
        ]);

        return view('aptika.rapat.show', compact('rapat'));
    }

    /**
     * Simpan notulensi rapat dan unggah dokumentasi foto hasil rapat.
     */
    public function notulensi(Request $request, RapatProject $rapat): RedirectResponse
    {
        $request->validate([
            'notulensi' => ['required', 'string'],
            'file_dokumentasi' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,zip', 'max:20480'],
            'status' => ['nullable', 'in:dijadwalkan,selesai,dibatalkan'],
        ]);

        $dokumentasiPath = $rapat->file_dokumentasi;
        if ($request->hasFile('file_dokumentasi')) {
            $file = $request->file('file_dokumentasi');
            $filename = 'dokumentasi_'.time().'.'.$file->getClientOriginalExtension();
            $dokumentasiPath = $file->storeAs("rapat/project_{$rapat->project_id}", $filename, 'public');
        }

        $rapat->update([
            'notulensi' => $request->input('notulensi'),
            'file_dokumentasi' => $dokumentasiPath,
            'status' => $request->input('status', 'selesai'),
        ]);

        return redirect()->route('aptika.rapat.show', $rapat)
            ->with('success', 'Notulensi dan dokumentasi rapat berhasil disimpan!');
    }
}
