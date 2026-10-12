<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\AplikasiRequest;
use App\Models\Aplikasi;
use App\Models\Instansi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AplikasiController extends Controller
{
    /**
     * Tampilkan daftar master aplikasi daerah.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $instansiFilter = $request->query('instansi_id');
        $statusFilter = $request->query('status');

        $aplikasis = Aplikasi::with('instansi')
            ->when($search, function ($query, $search) {
                $query->where('nama_aplikasi', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->when($instansiFilter, function ($query, $instansiId) {
                $query->where('instansi_id', $instansiId);
            })
            ->when($statusFilter, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $instansis = Instansi::orderBy('nama_instansi')->get();

        return view('superadmin.aplikasi.index', compact('aplikasis', 'instansis', 'search', 'instansiFilter', 'statusFilter'));
    }

    /**
     * Form entri aplikasi baru.
     */
    public function create(): View
    {
        $instansis = Instansi::orderBy('nama_instansi')->get();

        return view('superadmin.aplikasi.create', compact('instansis'));
    }

    /**
     * Simpan data aplikasi baru.
     */
    public function store(AplikasiRequest $request): RedirectResponse
    {
        Aplikasi::create($request->validated());

        return redirect()->route('superadmin.aplikasi.index')
            ->with('success', 'Aplikasi baru berhasil ditambahkan ke direktori sistem.');
    }

    /**
     * Form edit rincian aplikasi.
     */
    public function edit(Aplikasi $aplikasi): View
    {
        $instansis = Instansi::orderBy('nama_instansi')->get();

        return view('superadmin.aplikasi.edit', compact('aplikasi', 'instansis'));
    }

    /**
     * Perbarui data aplikasi.
     */
    public function update(AplikasiRequest $request, Aplikasi $aplikasi): RedirectResponse
    {
        $aplikasi->update($request->validated());

        return redirect()->route('superadmin.aplikasi.index')
            ->with('success', 'Rincian data aplikasi berhasil diperbarui.');
    }

    /**
     * Hapus data aplikasi.
     */
    public function destroy(Aplikasi $aplikasi): RedirectResponse
    {
        if ($aplikasi->pengajuan()->exists() || $aplikasi->projects()->exists()) {
            return back()->with('error', 'Aplikasi tidak dapat dihapus karena masih memiliki riwayat permohonan atau proyek terkait.');
        }

        $aplikasi->delete();

        return redirect()->route('superadmin.aplikasi.index')
            ->with('success', 'Data aplikasi berhasil dihapus.');
    }
}
