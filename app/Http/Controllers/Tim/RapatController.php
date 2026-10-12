<?php

namespace App\Http\Controllers\Tim;

use App\Http\Controllers\Controller;
use App\Models\PesertaRapat;
use App\Models\RapatProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RapatController extends Controller
{
    /**
     * Tampilkan daftar agenda rapat proyek yang mengundang user.
     */
    public function index(): View
    {
        $userId = auth()->id();

        $rapats = RapatProject::whereHas('peserta', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->with(['project.aplikasi', 'pembuat', 'peserta' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->orderByDesc('waktu_rapat')
            ->paginate(10);

        return view('tim.rapat.index', compact('rapats'));
    }

    /**
     * Tampilkan lembar rapat dan notulensi.
     */
    public function show(RapatProject $rapat): View
    {
        $userId = auth()->id();

        // Pastikan user adalah peserta rapat atau terlibat di proyek rapat ini
        $isParticipant = $rapat->peserta()->where('user_id', $userId)->exists();
        $isTeamMember = $rapat->project->tim()->where('user_id', $userId)->exists();

        if (! $isParticipant && ! $isTeamMember) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar dalam agenda rapat ini.');
        }

        $rapat->load(['project.aplikasi.instansi', 'pembuat', 'peserta.user']);

        $userPeserta = $rapat->peserta()->where('user_id', $userId)->first();

        return view('tim.rapat.show', compact('rapat', 'userPeserta'));
    }

    /**
     * Konfirmasi status kehadiran peserta rapat.
     */
    public function presensi(Request $request, RapatProject $rapat): RedirectResponse
    {
        $userId = auth()->id();

        $request->validate([
            'status_kehadiran' => ['required', 'in:hadir,izin,tidak_hadir'],
        ]);

        $peserta = PesertaRapat::where('rapat_project_id', $rapat->id)
            ->where('user_id', $userId)
            ->first();

        if (! $peserta) {
            // Jika belum ada di list peserta, buat baru
            $peserta = PesertaRapat::create([
                'rapat_project_id' => $rapat->id,
                'user_id' => $userId,
                'status_kehadiran' => $request->input('status_kehadiran'),
            ]);
        } else {
            $peserta->update([
                'status_kehadiran' => $request->input('status_kehadiran'),
            ]);
        }

        return back()->with('success', 'Konfirmasi kehadiran rapat berhasil disimpan.');
    }
}
