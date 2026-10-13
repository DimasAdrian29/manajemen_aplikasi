<?php

namespace App\Http\Controllers\Eksekutif;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eksekutif\MonitoringAplikasiRequest;
use App\Models\Instansi;
use App\Models\InventarisAplikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringAplikasiController extends Controller
{
    /**
     * Tampilkan monitoring daftar aplikasi Pemerintah Provinsi Riau beserta ringkasannya.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'instansi_id', 'jenis_layanan', 'status_pengembangan']);

        $inventaris = InventarisAplikasi::with(['instansi', 'aplikasiPengganti'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_aplikasi', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%")
                        ->orWhere('programmer', 'like', "%{$search}%")
                        ->orWhere('pengelola_adm', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['instansi_id'] ?? null, fn ($query, $instansiId) => $query->where('instansi_id', $instansiId))
            ->when($filters['jenis_layanan'] ?? null, fn ($query, $jenis) => $query->where('jenis_layanan', $jenis))
            ->when($filters['status_pengembangan'] ?? null, fn ($query, $status) => $query->where('status_pengembangan', $status))
            ->orderByRaw("CASE status WHEN 'aktif' THEN 1 WHEN 'tidak_aktif' THEN 2 WHEN 'diganti' THEN 3 ELSE 4 END")
            ->orderBy('nama_aplikasi')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'total' => InventarisAplikasi::count(),
            'aktif' => InventarisAplikasi::where('status', 'aktif')->count(),
            'tidak_aktif' => InventarisAplikasi::where('status', 'tidak_aktif')->count(),
            'diganti' => InventarisAplikasi::where('status', 'diganti')->count(),
            'publik' => InventarisAplikasi::where('jenis_layanan', 'publik')->count(),
            'pemerintahan' => InventarisAplikasi::where('jenis_layanan', 'pemerintahan')->count(),
            'website_resmi' => InventarisAplikasi::where('is_website_resmi', true)->count(),
            'dalam_pengembangan' => InventarisAplikasi::whereIn('status_pengembangan', ['on_progress', 'menunggu_konfirmasi'])->count(),
        ];

        $instansiList = Instansi::whereIn('id', InventarisAplikasi::select('instansi_id'))
            ->orderBy('nama_instansi')
            ->get(['id', 'nama_instansi']);

        return view('eksekutif.monitoring-aplikasi.index', compact('inventaris', 'ringkasan', 'instansiList', 'filters'));
    }

    /**
     * Tampilkan form pencatatan aplikasi baru.
     */
    public function create(): View
    {
        return view('eksekutif.monitoring-aplikasi.create', $this->formData(new InventarisAplikasi([
            'status' => 'aktif',
            'is_aplikasi_pemprov' => true,
            'platform' => 'web',
            'konektivitas' => 'online',
        ])));
    }

    /**
     * Simpan data aplikasi baru.
     */
    public function store(MonitoringAplikasiRequest $request): RedirectResponse
    {
        $aplikasi = InventarisAplikasi::create($this->normalisasi($request->validated()));

        return redirect()->route('eksekutif.monitoring-aplikasi.show', $aplikasi)
            ->with('success', "Aplikasi '{$aplikasi->nama_aplikasi}' berhasil ditambahkan ke daftar monitoring.");
    }

    /**
     * Tampilkan detail lengkap aplikasi.
     */
    public function show(InventarisAplikasi $monitoringAplikasi): View
    {
        $monitoringAplikasi->load(['instansi', 'aplikasiPengganti', 'aplikasiDigantikan']);

        return view('eksekutif.monitoring-aplikasi.show', ['aplikasi' => $monitoringAplikasi]);
    }

    /**
     * Tampilkan form ubah data aplikasi.
     */
    public function edit(InventarisAplikasi $monitoringAplikasi): View
    {
        return view('eksekutif.monitoring-aplikasi.edit', $this->formData($monitoringAplikasi));
    }

    /**
     * Perbarui data aplikasi.
     */
    public function update(MonitoringAplikasiRequest $request, InventarisAplikasi $monitoringAplikasi): RedirectResponse
    {
        $monitoringAplikasi->update($this->normalisasi($request->validated()));

        return redirect()->route('eksekutif.monitoring-aplikasi.show', $monitoringAplikasi)
            ->with('success', "Data aplikasi '{$monitoringAplikasi->nama_aplikasi}' berhasil diperbarui.");
    }

    /**
     * Hapus data aplikasi dari daftar monitoring.
     */
    public function destroy(InventarisAplikasi $monitoringAplikasi): RedirectResponse
    {
        if ($monitoringAplikasi->aplikasiDigantikan()->exists()) {
            return back()->with('error', 'Aplikasi ini tidak dapat dihapus karena tercatat sebagai pengganti aplikasi lain. Ubah dulu data aplikasi yang digantikan.');
        }

        $nama = $monitoringAplikasi->nama_aplikasi;
        $monitoringAplikasi->delete();

        return redirect()->route('eksekutif.monitoring-aplikasi.index')
            ->with('success', "Aplikasi '{$nama}' berhasil dihapus dari daftar monitoring.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(InventarisAplikasi $aplikasi): array
    {
        return [
            'aplikasi' => $aplikasi,
            'instansiList' => Instansi::orderBy('nama_instansi')->get(['id', 'nama_instansi']),
            'penggantiList' => InventarisAplikasi::when($aplikasi->exists, fn ($q) => $q->whereKeyNot($aplikasi->id))
                ->orderBy('nama_aplikasi')
                ->get(['id', 'nomor_registrasi', 'nama_aplikasi']),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalisasi(array $data): array
    {
        if (($data['status'] ?? null) !== 'diganti') {
            $data['aplikasi_pengganti_id'] = null;
        }

        return $data;
    }
}
