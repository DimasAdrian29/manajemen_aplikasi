<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\MasterDokumenRequest;
use App\Models\MasterDokumenPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MasterDokumenController extends Controller
{
    /**
     * Tampilkan daftar master jenis dokumen regulasi pengajuan.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $berlakuUntuk = $request->query('berlaku_untuk');

        $dokumens = MasterDokumenPengajuan::withCount('dokumen')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_dokumen', 'like', "%{$search}%")
                        ->orWhere('kode_dokumen', 'like', "%{$search}%");
                });
            })
            ->when($berlakuUntuk, function ($query, $berlakuUntuk) {
                $query->where('berlaku_untuk', $berlakuUntuk);
            })
            ->orderBy('urutan', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.master-dokumen.index', compact('dokumens', 'search', 'berlakuUntuk'));
    }

    /**
     * Form tambah jenis dokumen baru.
     */
    public function create(): View
    {
        $nextUrutan = (MasterDokumenPengajuan::max('urutan') ?? 0) + 1;

        return view('superadmin.master-dokumen.create', compact('nextUrutan'));
    }

    /**
     * Simpan jenis dokumen baru.
     */
    public function store(MasterDokumenRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file_template')) {
            $file = $request->file('file_template');
            $filename = 'template_'.$request->input('kode_dokumen').'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('templates', $filename, 'public');
            $data['file_template'] = $path;
        }

        MasterDokumenPengajuan::create($data);

        return redirect()->route('superadmin.master-dokumen.index')
            ->with('success', 'Master jenis dokumen berhasil ditambahkan.');
    }

    /**
     * Form edit master jenis dokumen.
     */
    public function edit(MasterDokumenPengajuan $masterDokumen): View
    {
        return view('superadmin.master-dokumen.edit', compact('masterDokumen'));
    }

    /**
     * Perbarui data master jenis dokumen.
     */
    public function update(MasterDokumenRequest $request, MasterDokumenPengajuan $masterDokumen): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file_template')) {
            if ($masterDokumen->file_template && Storage::disk('public')->exists($masterDokumen->file_template)) {
                Storage::disk('public')->delete($masterDokumen->file_template);
            }

            $file = $request->file('file_template');
            $filename = 'template_'.$request->input('kode_dokumen').'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('templates', $filename, 'public');
            $data['file_template'] = $path;
        }

        $masterDokumen->update($data);

        return redirect()->route('superadmin.master-dokumen.index')
            ->with('success', 'Master jenis dokumen berhasil diperbarui.');
    }

    /**
     * Hapus jenis master dokumen.
     */
    public function destroy(MasterDokumenPengajuan $masterDokumen): RedirectResponse
    {
        if ($masterDokumen->dokumen()->exists()) {
            return back()->with('error', 'Dokumen ini tidak dapat dihapus karena sudah digunakan dalam pengajuan yang ada. Anda dapat menonaktifkannya.');
        }

        if ($masterDokumen->file_template && Storage::disk('public')->exists($masterDokumen->file_template)) {
            Storage::disk('public')->delete($masterDokumen->file_template);
        }

        $masterDokumen->delete();

        return redirect()->route('superadmin.master-dokumen.index')
            ->with('success', 'Master jenis dokumen berhasil dihapus.');
    }

    /**
     * Ubah status aktif/nonaktif dokumen dengan cepat.
     */
    public function toggleStatus(MasterDokumenPengajuan $masterDokumen): RedirectResponse
    {
        $masterDokumen->update([
            'is_active' => ! $masterDokumen->is_active,
        ]);

        $status = $masterDokumen->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Jenis dokumen {$masterDokumen->nama_dokumen} berhasil {$status}.");
    }
}
