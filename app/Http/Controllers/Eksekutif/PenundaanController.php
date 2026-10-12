<?php

namespace App\Http\Controllers\Eksekutif;

use App\Http\Controllers\Controller;
use App\Models\Penundaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenundaanController extends Controller
{
    /**
     * Tampilkan antrean permohonan penundaan proyek yang diajukan oleh Tim Pengembang.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'diajukan');

        $query = Penundaan::with(['project.aplikasi.instansi', 'pengaju']);

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $penundaans = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'diajukan' => Penundaan::where('status', 'diajukan')->count(),
            'disetujui' => Penundaan::where('status', 'disetujui')->count(),
            'ditolak' => Penundaan::where('status', 'ditolak')->count(),
            'total' => Penundaan::count(),
        ];

        return view('eksekutif.penundaan.index', compact('penundaans', 'counts', 'status'));
    }

    /**
     * Setujui permohonan penundaan target waktu proyek.
     */
    public function approve(Request $request, Penundaan $penundaan): RedirectResponse
    {
        if ($penundaan->status !== 'diajukan') {
            return back()->with('error', 'Permohonan penundaan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'tanggal_selesai_baru' => ['nullable', 'date', 'after:today'],
        ]);

        DB::transaction(function () use ($request, $penundaan) {
            $penundaan->update([
                'status' => 'disetujui',
            ]);

            $project = $penundaan->project;
            $updateData = ['status' => 'delayed'];

            if ($request->filled('tanggal_selesai_baru')) {
                $updateData['tanggal_selesai'] = $request->input('tanggal_selesai_baru');
            }

            $project->update($updateData);
        });

        return back()->with('success', "Permohonan penundaan untuk proyek '{$penundaan->project->nama_project}' berhasil disetujui.");
    }

    /**
     * Tolak permohonan penundaan target waktu proyek.
     */
    public function reject(Request $request, Penundaan $penundaan): RedirectResponse
    {
        if ($penundaan->status !== 'diajukan') {
            return back()->with('error', 'Permohonan penundaan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($penundaan) {
            $penundaan->update([
                'status' => 'ditolak',
            ]);

            // Kembalikan status proyek ke in_development agar tim melanjutkan pengerjaan
            $penundaan->project->update([
                'status' => 'in_development',
            ]);
        });

        return back()->with('success', "Permohonan penundaan untuk proyek '{$penundaan->project->nama_project}' telah ditolak. Status proyek dikembalikan ke pengembangan aktif.");
    }
}
