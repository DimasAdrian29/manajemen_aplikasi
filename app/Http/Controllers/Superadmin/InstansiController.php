<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\InstansiRequest;
use App\Models\Instansi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstansiController extends Controller
{
    /**
     * Tampilkan daftar master instansi OPD.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $instansis = Instansi::withCount(['users', 'aplikasi'])
            ->when($search, function ($query, $search) {
                $query->where('nama_instansi', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.instansi.index', compact('instansis', 'search'));
    }

    /**
     * Form tambah instansi baru.
     */
    public function create(): View
    {
        return view('superadmin.instansi.create');
    }

    /**
     * Simpan instansi baru.
     */
    public function store(InstansiRequest $request): RedirectResponse
    {
        Instansi::create($request->validated());

        return redirect()->route('superadmin.instansi.index')
            ->with('success', 'Instansi OPD berhasil didaftarkan ke sistem.');
    }

    /**
     * Form edit data instansi.
     */
    public function edit(Instansi $instansi): View
    {
        return view('superadmin.instansi.edit', compact('instansi'));
    }

    /**
     * Perbarui data instansi.
     */
    public function update(InstansiRequest $request, Instansi $instansi): RedirectResponse
    {
        $instansi->update($request->validated());

        return redirect()->route('superadmin.instansi.index')
            ->with('success', 'Data Instansi OPD berhasil diperbarui.');
    }

    /**
     * Hapus instansi.
     */
    public function destroy(Instansi $instansi): RedirectResponse
    {
        // Proteksi jika instansi masih terhubung dengan user atau aplikasi
        if ($instansi->users()->exists() || $instansi->aplikasi()->exists()) {
            return back()->with('error', 'Instansi tidak dapat dihapus karena masih memiliki pengguna atau aplikasi yang terhubung.');
        }

        $instansi->delete();

        return redirect()->route('superadmin.instansi.index')
            ->with('success', 'Instansi OPD berhasil dihapus.');
    }
}
