<?php

namespace App\Http\Controllers\Aptika;

use App\Http\Controllers\Controller;
use App\Models\MasterDokumenPengajuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MasterDokumenController extends Controller
{
    /**
     * Ambil data detail jenis dokumen untuk keperluan edit cepat.
     */
    public function show(MasterDokumenPengajuan $masterDokumen): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $masterDokumen,
        ]);
    }

    /**
     * Simpan jenis dokumen baru via AJAX inline modal.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_dokumen' => ['required', 'string', 'max:255'],
            'kode_dokumen' => ['nullable', 'string', 'max:100', 'alpha_dash', 'unique:master_dokumen_pengajuan,kode_dokumen'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'format_diizinkan' => ['nullable', 'string', 'max:50'],
            'max_size_kb' => ['nullable', 'integer', 'min:100', 'max:51200'],
            'wajib' => ['nullable', 'boolean'],
            'berlaku_untuk' => ['nullable', Rule::in(['semua', 'new_app', 'app_development', 'app_deactivation'])],
        ]);

        $baseKode = ! empty($validated['kode_dokumen'])
            ? Str::slug($validated['kode_dokumen'], '_')
            : Str::slug($validated['nama_dokumen'], '_');

        $kode = $baseKode;
        $counter = 1;
        while (MasterDokumenPengajuan::where('kode_dokumen', $kode)->exists()) {
            $kode = "{$baseKode}_{$counter}";
            $counter++;
        }

        $nextUrutan = (MasterDokumenPengajuan::max('urutan') ?? 0) + 1;

        $doc = MasterDokumenPengajuan::create([
            'nama_dokumen' => $validated['nama_dokumen'],
            'kode_dokumen' => $kode,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'format_diizinkan' => ! empty($validated['format_diizinkan']) ? $validated['format_diizinkan'] : 'pdf',
            'max_size_kb' => ! empty($validated['max_size_kb']) ? (int) $validated['max_size_kb'] : 10240,
            'wajib' => $request->boolean('wajib', true),
            'berlaku_untuk' => ! empty($validated['berlaku_untuk']) ? $validated['berlaku_untuk'] : 'semua',
            'is_active' => true,
            'urutan' => $nextUrutan,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Jenis dokumen '{$doc->nama_dokumen}' berhasil ditambahkan.",
            'data' => $doc,
        ], 201);
    }

    /**
     * Perbarui data jenis dokumen via AJAX inline modal.
     */
    public function update(Request $request, MasterDokumenPengajuan $masterDokumen): JsonResponse
    {
        $validated = $request->validate([
            'nama_dokumen' => ['required', 'string', 'max:255'],
            'kode_dokumen' => [
                'nullable',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('master_dokumen_pengajuan', 'kode_dokumen')->ignore($masterDokumen->id),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'format_diizinkan' => ['nullable', 'string', 'max:50'],
            'max_size_kb' => ['nullable', 'integer', 'min:100', 'max:51200'],
            'wajib' => ['nullable', 'boolean'],
            'berlaku_untuk' => ['nullable', Rule::in(['semua', 'new_app', 'app_development', 'app_deactivation'])],
        ]);

        $updateData = [
            'nama_dokumen' => $validated['nama_dokumen'],
            'deskripsi' => array_key_exists('deskripsi', $validated) ? $validated['deskripsi'] : $masterDokumen->deskripsi,
            'format_diizinkan' => ! empty($validated['format_diizinkan']) ? $validated['format_diizinkan'] : $masterDokumen->format_diizinkan,
            'max_size_kb' => ! empty($validated['max_size_kb']) ? (int) $validated['max_size_kb'] : $masterDokumen->max_size_kb,
            'wajib' => $request->has('wajib') ? $request->boolean('wajib') : $masterDokumen->wajib,
        ];

        if (! empty($validated['kode_dokumen'])) {
            $updateData['kode_dokumen'] = Str::slug($validated['kode_dokumen'], '_');
        }

        if (! empty($validated['berlaku_untuk'])) {
            $updateData['berlaku_untuk'] = $validated['berlaku_untuk'];
        }

        $masterDokumen->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Jenis dokumen '{$masterDokumen->nama_dokumen}' berhasil diperbarui.",
            'data' => $masterDokumen,
        ]);
    }

    /**
     * Hapus jenis dokumen dengan verifikasi integritas berkas (Safety Guard).
     */
    public function destroy(MasterDokumenPengajuan $masterDokumen): JsonResponse
    {
        // 1. Safety Guard: Periksa apakah ada berkas fisik pengajuan yang pernah diunggah
        if ($masterDokumen->dokumen()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Jenis dokumen '{$masterDokumen->nama_dokumen}' tidak dapat dihapus karena sudah digunakan dalam riwayat pengajuan OPD.",
            ], 422);
        }

        // 2. Safety Guard: Periksa apakah sedang dikaitkan pada field alur kerja lain
        if ($masterDokumen->workflowFields()->exists()) {
            $fieldNames = $masterDokumen->workflowFields()->pluck('label')->implode(', ');

            return response()->json([
                'success' => false,
                'message' => "Jenis dokumen '{$masterDokumen->nama_dokumen}' sedang dipakai pada konfigurasi field workflow ({$fieldNames}). Lepaskan field terlebih dahulu sebelum menghapus.",
            ], 422);
        }

        if ($masterDokumen->file_template && Storage::disk('public')->exists($masterDokumen->file_template)) {
            Storage::disk('public')->delete($masterDokumen->file_template);
        }

        $name = $masterDokumen->nama_dokumen;
        $masterDokumen->delete();

        return response()->json([
            'success' => true,
            'message' => "Jenis dokumen '{$name}' berhasil dihapus.",
        ]);
    }
}
