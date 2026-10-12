<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Opd\PengajuanRequest;
use App\Models\Aplikasi;
use App\Models\DokumenPengajuan;
use App\Models\Layanan;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\PengajuanFieldData;
use App\Models\PengajuanWorkflowData;
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
        $layananId = $request->query('layanan_id');

        $layanans = Layanan::where('is_active', true)->orderBy('urutan')->get();

        $pengajuans = Pengajuan::where('instansi_id', $instansiId)
            ->with(['layanan', 'aplikasi', 'dokumen.masterDokumen', 'workflowData.layananWorkflow.workflow'])
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
            ->when($layananId, function ($query, $layananId) {
                $query->where('layanan_id', $layananId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('opd.pengajuan.index', compact('pengajuans', 'layanans', 'search', 'status', 'jenis', 'layananId'));
    }

    /**
     * Tampilkan formulir pembuatan permohonan baru dengan alur dan field workflow dinamis.
     */
    public function create(Request $request): View
    {
        $instansiId = auth()->user()->instansi_id;
        $aplikasis = Aplikasi::where('instansi_id', $instansiId)->orderBy('nama_aplikasi')->get();

        $layanans = Layanan::where('is_active', true)
            ->with(['workflows.workflow.fields.masterDokumen'])
            ->orderBy('urutan')
            ->get();

        $selectedLayanan = null;
        if ($request->filled('layanan')) {
            $selectedLayanan = $layanans->firstWhere('slug', $request->query('layanan'))
                ?? $layanans->firstWhere('id', $request->query('layanan'));
        } elseif ($request->filled('jenis')) {
            $matchedSlug = match ($request->query('jenis')) {
                'new_app' => 'pembangunan-aplikasi-baru',
                'app_development' => 'pengembangan-aplikasi-eksisting',
                'app_deactivation' => 'penonaktifan-aplikasi',
                default => null,
            };
            if ($matchedSlug) {
                $selectedLayanan = $layanans->firstWhere('slug', $matchedSlug);
            }
        }

        if (! $selectedLayanan) {
            $selectedLayanan = $layanans->first();
        }

        $firstStepLw = $selectedLayanan?->workflows->first();
        $firstStepWorkflow = $firstStepLw?->workflow;

        $masterDokumens = MasterDokumenPengajuan::active()->orderBy('urutan')->get();

        return view('opd.pengajuan.create', compact('aplikasis', 'layanans', 'selectedLayanan', 'firstStepLw', 'firstStepWorkflow', 'masterDokumens'));
    }

    /**
     * Simpan permohonan baru dan inisialisasi status tahapan workflow serta isian field.
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
            if ($request->filled('aplikasi_id')) {
                $app = Aplikasi::where('instansi_id', $instansiId)->find($request->input('aplikasi_id'));
                if ($app && empty($namaAplikasi)) {
                    $namaAplikasi = $app->nama_aplikasi;
                }
            }

            // 2. Simpan Data Pokok Pengajuan
            $pengajuan = Pengajuan::create([
                'kode_pengajuan' => $kodePengajuan,
                'instansi_id' => $instansiId,
                'layanan_id' => $request->input('layanan_id'),
                'aplikasi_id' => $request->input('aplikasi_id'),
                'jenis_pengajuan' => $request->input('jenis_pengajuan'),
                'nama_aplikasi' => $namaAplikasi,
                'deskripsi_kebutuhan' => $request->input('deskripsi_kebutuhan'),
                'nama_pemohon' => $request->input('nama_pemohon'),
                'no_hp_pemohon' => $request->input('no_hp_pemohon'),
                'status' => 'submitted',
            ]);

            // 3. Inisialisasi Alur Pipeline Workflow untuk Pengajuan ini
            $layanan = $pengajuan->layanan ? $pengajuan->layanan->load('workflows.workflow.fields') : null;
            $firstStepData = null;

            if ($layanan) {
                foreach ($layanan->workflows as $index => $lw) {
                    $status = ($index === 0) ? 'completed' : 'pending';
                    $pwData = PengajuanWorkflowData::create([
                        'pengajuan_id' => $pengajuan->id,
                        'layanan_workflow_id' => $lw->id,
                        'status' => $status,
                        'completed_by' => ($index === 0) ? auth()->id() : null,
                        'completed_at' => ($index === 0) ? now() : null,
                    ]);

                    if ($index === 0) {
                        $firstStepData = $pwData;
                    }
                }
            }

            // 4. Simpan Dokumen Terisolasi & Link ke Field Data
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

                // Simpan juga ke PengajuanFieldData jika field pertama terkait master doc ini
                if ($firstStepData) {
                    $relatedField = $firstStepData->layananWorkflow->workflow->fields
                        ->firstWhere('master_dokumen_id', $masterDoc->id);
                    if ($relatedField) {
                        PengajuanFieldData::create([
                            'pengajuan_workflow_data_id' => $firstStepData->id,
                            'workflow_field_id' => $relatedField->id,
                            'file_path' => $path,
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'ukuran_file' => $file->getSize(),
                        ]);
                    }
                }

                if ($masterDoc->kode_dokumen === 'proposal_teknis') {
                    $legacyProposal = $path;
                } elseif ($masterDoc->kode_dokumen === 'surat_pengantar') {
                    $legacySurat = $path;
                }
            }

            // 5. Simpan Field Checklist & Isian Teks
            $formFields = $request->input('fields', []);
            if ($firstStepData && ! empty($formFields)) {
                foreach ($formFields as $fieldId => $fieldValue) {
                    $wfField = $firstStepData->layananWorkflow->workflow->fields->find($fieldId);
                    if ($wfField) {
                        PengajuanFieldData::create([
                            'pengajuan_workflow_data_id' => $firstStepData->id,
                            'workflow_field_id' => $wfField->id,
                            'value' => (string) $fieldValue,
                        ]);
                    }
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
     * Tampilkan detail pelacakan status, berkas, dan visualisasi stepper pipeline workflow.
     */
    public function show(Pengajuan $pengajuan): View
    {
        $instansiId = auth()->user()->instansi_id;
        abort_if($pengajuan->instansi_id !== $instansiId, 403, 'Anda tidak memiliki hak akses terhadap data instansi lain.');

        $pengajuan->load([
            'layanan.workflows.workflow',
            'instansi',
            'aplikasi',
            'penilai',
            'dokumen.masterDokumen',
            'project',
            'workflowData' => function ($q) {
                $q->with(['layananWorkflow.workflow', 'fieldData.workflowField', 'completedBy']);
            },
        ]);

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

        Storage::disk('public')->deleteDirectory("pengajuan/{$pengajuan->kode_pengajuan}");
        $pengajuan->delete();

        return redirect()->route('opd.pengajuan.index')
            ->with('success', 'Permohonan pengajuan layanan berhasil dibatalkan dan berkas telah dihapus.');
    }
}
