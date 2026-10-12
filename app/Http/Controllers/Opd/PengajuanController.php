<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Opd\PengajuanRequest;
use App\Models\Aplikasi;
use App\Models\DokumenPengajuan;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Tampilkan riwayat permohonan layanan milik OPD terkait.
     */
    public function index(Request $request): View
    {
        $instansiId = auth()->user()->instansi_id;
        $search = $request->query('search');
        $status = $request->query('status');
        $jenis = $request->query('jenis');

        $pengajuans = Pengajuan::where('instansi_id', $instansiId)
            ->with(['aplikasi', 'dokumen.masterDokumen'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_pengajuan', 'like', "%{$search}%")
                        ->orWhere('nama_aplikasi', 'like', "%{$search}%")
                        ->orWhere('nama_pemohon', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($jenis, function ($query, $jenis) {
                $query->where('jenis_pengajuan', $jenis);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('opd.pengajuan.index', compact('pengajuans', 'search', 'status', 'jenis'));
    }

    /**
     * Tampilkan formulir pembuatan permohonan baru dengan slot dokumen dinamis.
     */
    public function create(): View
    {
        $instansiId = auth()->user()->instansi_id;
        $aplikasis = Aplikasi::where('instansi_id', $instansiId)->orderBy('nama_aplikasi')->get();
        $masterDokumens = MasterDokumenPengajuan::active()->orderBy('urutan')->get();

        return view('opd.pengajuan.create', compact('aplikasis', 'masterDokumens'));
    }

    /**
     * Simpan permohonan baru dan simpan dokumen ke folder terisolasi.
     */
    public function store(PengajuanRequest $request): RedirectResponse
    {
        $instansiId = auth()->user()->instansi_id;

        return DB::transaction(function () use ($request, $instansiId) {
            // 1. Generate Nomor Registrasi Unik REG-YYYYMMDD-XXXX
            $today = Carbon::now()->format('Ymd');
            $prefix = "REG-{$today}-";
            $countToday = Pengajuan::where('kode_pengajuan', 'like', "{$prefix}%")->count();
            $kodePengajuan = $prefix.str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

            $namaAplikasi = $request->input('nama_aplikasi');
            if (in_array($request->input('jenis_pengajuan'), ['app_development', 'app_deactivation']) && $request->filled('aplikasi_id')) {
                $app = Aplikasi::where('instansi_id', $instansiId)->find($request->input('aplikasi_id'));
                if ($app) {
                    $namaAplikasi = $app->nama_aplikasi;
                }
            }

            // 2. Simpan Data Pokok Pengajuan
            $pengajuan = Pengajuan::create([
                'kode_pengajuan' => $kodePengajuan,
                'instansi_id' => $instansiId,
                'aplikasi_id' => $request->input('aplikasi_id'),
                'jenis_pengajuan' => $request->input('jenis_pengajuan'),
                'nama_aplikasi' => $namaAplikasi,
                'deskripsi_kebutuhan' => $request->input('deskripsi_kebutuhan'),
                'nama_pemohon' => $request->input('nama_pemohon'),
                'no_hp_pemohon' => $request->input('no_hp_pemohon'),
                'status' => 'submitted',
            ]);

            // 3. Simpan Berkas Dokumen Terisolasi per Jenis Dokumen
            // Format direktori: storage/app/public/pengajuan/{kode_pengajuan}/{kode_dokumen}/{timestamp}_{nama_file_bersih}.ext
            $uploadedFiles = $request->file('dokumen', []);
            $legacyProposal = null;
            $legacySurat = null;

            foreach ($uploadedFiles as $masterDokumenId => $file) {
                if (! $file) {
                    continue;
                }

                $masterDoc = MasterDokumenPengajuan::find($masterDokumenId);
                if (! $masterDoc) {
                    continue;
                }

                $folderPath = "pengajuan/{$kodePengajuan}/{$masterDoc->kode_dokumen}";
                $cleanOriginalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $cleanSlug = Str::slug($cleanOriginalName);
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$cleanSlug.'.'.$extension;

                $path = $file->storeAs($folderPath, $fileName, 'public');

                DokumenPengajuan::create([
                    'pengajuan_id' => $pengajuan->id,
                    'master_dokumen_id' => $masterDoc->id,
                    'nama_file_asli' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'ukuran_file' => $file->getSize(),
                ]);

                // Sinkronisasi kolom legacy jika nama kode sesuai
                if ($masterDoc->kode_dokumen === 'proposal_teknis') {
                    $legacyProposal = $path;
                } elseif ($masterDoc->kode_dokumen === 'surat_pengantar') {
                    $legacySurat = $path;
                }
            }

            if ($legacyProposal || $legacySurat) {
                $pengajuan->update([
                    'file_proposal' => $legacyProposal,
                    'file_surat_pengantar' => $legacySurat,
                ]);
            }

            return redirect()->route('opd.pengajuan.show', $pengajuan)
                ->with('success', "Permohonan berhasil dikirim dengan Nomor Registrasi {$kodePengajuan}. Tim Kominfo akan segera melakukan telaah teknis.");
        });
    }

    /**
     * Tampilkan detail pelacakan status dan berkas pengajuan.
     */
    public function show(Pengajuan $pengajuan): View
    {
        $instansiId = auth()->user()->instansi_id;
        abort_if($pengajuan->instansi_id !== $instansiId, 403, 'Anda tidak memiliki hak akses terhadap data instansi lain.');

        $pengajuan->load(['instansi', 'aplikasi', 'penilai', 'dokumen.masterDokumen', 'project']);

        return view('opd.pengajuan.show', compact('pengajuan'));
    }

    /**
     * Hapus pengajuan (hanya jika berstatus submitted atau draft).
     */
    public function destroy(Pengajuan $pengajuan): RedirectResponse
    {
        $instansiId = auth()->user()->instansi_id;
        abort_if($pengajuan->instansi_id !== $instansiId, 403);

        if (! in_array($pengajuan->status, ['draft', 'submitted'])) {
            return back()->with('error', 'Permohonan yang sedang dalam tahap penilaian atau sudah disetujui tidak dapat dibatalkan.');
        }

        // Hapus folder penyimpanan berkas terisolasi
        Storage::disk('public')->deleteDirectory("pengajuan/{$pengajuan->kode_pengajuan}");

        $pengajuan->delete();

        return redirect()->route('opd.pengajuan.index')
            ->with('success', 'Permohonan pengajuan layanan berhasil dibatalkan dan berkas telah dihapus.');
    }
}
