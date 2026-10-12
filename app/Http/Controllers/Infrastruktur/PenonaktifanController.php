<?php

namespace App\Http\Controllers\Infrastruktur;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\RiwayatHosting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenonaktifanController extends Controller
{
    /**
     * Display applications queued for deactivation / decommissioning.
     */
    public function index(Request $request): View
    {
        $query = Pengajuan::with([
            'aplikasi.instansi',
            'aplikasi.projects.hostingAplikasi',
            'penilai',
            'dokumen.masterDokumen',
        ])
            ->where('jenis_pengajuan', 'app_deactivation')
            ->where('status', 'approved');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                    ->orWhere('nama_aplikasi', 'like', "%{$search}%")
                    ->orWhereHas('instansi', function ($iQ) use ($search) {
                        $iQ->where('nama_instansi', 'like', "%{$search}%");
                    });
            });
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        // Also get already decommissioned list for reference tab
        $decommissioned = Pengajuan::with([
            'aplikasi.instansi',
            'penilai',
        ])
            ->where('jenis_pengajuan', 'app_deactivation')
            ->where('status', 'approved')
            ->whereHas('aplikasi', function ($q) {
                $q->where('status', 'nonaktif');
            })
            ->latest()
            ->take(10)
            ->get();

        return view('infrastruktur.penonaktifan.index', compact('pengajuans', 'decommissioned'));
    }

    /**
     * Confirm application deactivation, close server and domain.
     */
    public function execute(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        if ($pengajuan->jenis_pengajuan !== 'app_deactivation' || $pengajuan->status !== 'approved') {
            return redirect()
                ->route('infrastruktur.penonaktifan.index')
                ->with('error', 'Pengajuan penonaktifan ini tidak valid untuk diproses.');
        }

        $validated = $request->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($pengajuan, $validated) {
            $aplikasi = $pengajuan->aplikasi;

            if ($aplikasi) {
                $aplikasi->update(['status' => 'nonaktif']);

                // Find hosting via projects
                foreach ($aplikasi->projects as $project) {
                    $project->update(['status' => 'inactive']);

                    if ($hosting = $project->hostingAplikasi) {
                        // Snapshot prior data
                        RiwayatHosting::create([
                            'hosting_aplikasi_id' => $hosting->id,
                            'user_id' => auth()->id(),
                            'data_sebelum' => [
                                'nama_domain' => $hosting->nama_domain,
                                'url_aplikasi' => $hosting->url_aplikasi,
                                'ip_server' => $hosting->ip_server,
                                'nama_server' => $hosting->nama_server,
                                'status_hosting' => $hosting->status_hosting,
                            ],
                            'alasan_perubahan' => 'Penonaktifan aplikasi dan penutupan server sesuai permohonan OPD: '.($validated['catatan'] ?? 'Disetujui Bidang APTIKA'),
                        ]);

                        $hosting->update([
                            'status_hosting' => 'nonaktif',
                            'keterangan' => ($hosting->keterangan ? $hosting->keterangan."\n" : '').
                                'Dinonaktifkan oleh Admin Infrastruktur pada '.now()->format('d/m/Y H:i').
                                ($validated['catatan'] ? '. Catatan: '.$validated['catatan'] : ''),
                        ]);
                    }
                }
            }

            // Update pengajuan catatan
            $pengajuan->update([
                'catatan_penilaian' => ($pengajuan->catatan_penilaian ? $pengajuan->catatan_penilaian."\n" : '').
                    '[Infrastruktur '.now()->format('d/m/Y H:i').'] Server dan domain aplikasi telah dinonaktifkan.'.
                    ($validated['catatan'] ? ' Catatan: '.$validated['catatan'] : ''),
            ]);
        });

        return redirect()
            ->route('infrastruktur.penonaktifan.index')
            ->with('success', "Aplikasi '{$pengajuan->nama_aplikasi}' dan infrastruktur terkait berhasil dinonaktifkan.");
    }
}
