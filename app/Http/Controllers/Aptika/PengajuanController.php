<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Tampilkan daftar seluruh usulan pengajuan dari semua OPD.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $jenis = $request->query('jenis');
        $instansiId = $request->query('instansi_id');

        $instansis = Instansi::orderBy('nama_instansi')->get();

        $pengajuans = Pengajuan::with(['instansi', 'aplikasi', 'dokumen.masterDokumen', 'penilai'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_pengajuan', 'like', "%{$search}%")
                        ->orWhere('nama_aplikasi', 'like', "%{$search}%")
                        ->orWhere('nama_pemohon', 'like', "%{$search}%")
                        ->orWhereHas('instansi', function ($iq) use ($search) {
                            $iq->where('nama_instansi', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($jenis, function ($query, $jenis) {
                $query->where('jenis_pengajuan', $jenis);
            })
            ->when($instansiId, function ($query, $instansiId) {
                $query->where('instansi_id', $instansiId);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('aptika.pengajuan.index', compact('pengajuans', 'instansis', 'search', 'status', 'jenis', 'instansiId'));
    }

    /**
     * Tampilkan detail permohonan untuk telaah dokumen & persetujuan.
     */
    public function show(Pengajuan $pengajuan): View
    {
        $pengajuan->load(['instansi', 'aplikasi', 'dokumen.masterDokumen', 'penilai', 'project.tim.user']);

        return view('aptika.pengajuan.show', compact('pengajuan'));
    }

    /**
     * Proses telaah: Mulai Telaah, Setujui, atau Tolak Permohonan.
     */
    public function review(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:under_review,approved,rejected'],
            'catatan_penilaian' => ['nullable', 'string', 'max:2000', 'required_if:status,rejected'],
        ], [
            'catatan_penilaian.required_if' => 'Alasan penolakan / catatan telaah wajib diisi jika permohonan ditolak.',
        ]);

        $pengajuan->update([
            'status' => $request->input('status'),
            'catatan_penilaian' => $request->input('catatan_penilaian'),
            'dinilai_oleh' => auth()->id(),
            'tanggal_penilaian' => Carbon::now(),
        ]);

        $statusLabel = match ($request->input('status')) {
            'under_review' => 'telah diubah ke tahap Dalam Telaah Teknis',
            'approved' => 'berhasil Disetujui. Anda sekarang dapat menginisiasi proyek pengembangan.',
            'rejected' => 'telah Ditolak dengan catatan perbaikan.',
            default => 'berhasil diperbarui.',
        };

        if ($request->input('status') === 'approved') {
            return redirect()->route('aptika.project.create', ['pengajuan_id' => $pengajuan->id])
                ->with('success', "Permohonan {$pengajuan->kode_pengajuan} disetujui! Lanjutkan penentuan tim dan inisiasi proyek.");
        }

        return redirect()->route('aptika.pengajuan.show', $pengajuan)
            ->with('success', "Status permohonan {$pengajuan->kode_pengajuan} {$statusLabel}");
    }
}
