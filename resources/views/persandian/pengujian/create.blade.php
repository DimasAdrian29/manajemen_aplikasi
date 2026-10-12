@extends('layouts.persandian')

@section('title', 'Audit Keamanan - ' . $project->nama_project)
@section('page_title', 'Form Audit Keamanan Informasi & Pentest')
@section('page_subtitle', 'Pencatatan hasil pengujian kerentanan siber, analisis risiko, dan penerbitan rekomendasi kelaikan operasi')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('persandian.pengujian.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Antrean Uji Keamanan</span>
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
            Tahap: Security Testing
        </span>
    </div>

    <!-- Project Information Card -->
    <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block">Target Audit Keamanan</span>
                <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $project->nama_project }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Instansi: <strong class="text-slate-700">{{ $project->aplikasi?->instansi?->nama_instansi ?? 'Pemerintah Provinsi Riau' }}</strong> &bull;
                    Aplikasi: <strong class="text-slate-700">{{ $project->aplikasi?->nama_aplikasi ?? '-' }}</strong>
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[11px] text-slate-400 block">Ketua Tim Pengembang</span>
                    <span class="text-xs font-bold text-slate-800">
                        {{ $project->tim->where('peran', 'ketua_tim')->first()?->user?->name ?? 'Belum Ditentukan' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Kode Permohonan</span>
                <span class="font-bold text-slate-800">{{ $project->pengajuan?->kode_pengajuan ?? '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Status Uji Fungsional</span>
                <span class="font-bold text-emerald-700 flex items-center gap-1 mt-0.5">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Lulus Pengujian APTIKA
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Capaian Progres Proyek</span>
                <span class="font-bold text-[#0D47A1]">{{ $project->progres_total }}%</span>
            </div>
        </div>
    </div>

    <!-- Security Audit Form -->
    <form method="POST" action="{{ route('persandian.pengujian.store', $project) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Section 1: Parameter & Hasil Audit -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-emerald-700">security</span>
                <h3 class="text-sm font-bold text-slate-900">1. Parameter Pengujian & Hasil Evaluasi CSIRT</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Metode / Kategori Pengujian <span class="text-rose-500">*</span>
                    </label>
                    <select name="kategori_pengujian" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="penetration_testing" {{ old('kategori_pengujian') === 'penetration_testing' ? 'selected' : '' }}>Penetration Testing (Uji Penetrasi Web/API)</option>
                        <option value="vulnerability_assessment" {{ old('kategori_pengujian') === 'vulnerability_assessment' ? 'selected' : '' }}>Vulnerability Assessment (Pemindaian Kerentanan)</option>
                        <option value="source_code_review" {{ old('kategori_pengujian') === 'source_code_review' ? 'selected' : '' }}>Source Code Review (SAST / Audit Kode)</option>
                        <option value="audit_konfigurasi" {{ old('kategori_pengujian') === 'audit_konfigurasi' ? 'selected' : '' }}>Audit Konfigurasi Keamanan Server</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tanggal Pelaksanaan Pengujian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_uji" value="{{ old('tanggal_uji', now()->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Hasil Evaluasi Keamanan <span class="text-rose-500">*</span>
                    </label>
                    <select name="hasil" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="lulus" {{ old('hasil', 'lulus') === 'lulus' ? 'selected' : '' }}>Lulus (Laik Operasi - Lanjut ke Deployment)</option>
                        <option value="perbaikan" {{ old('hasil') === 'perbaikan' ? 'selected' : '' }}>Perbaikan (Celah ditemukan - Kembalikan ke Tim Pengembang)</option>
                        <option value="tidak_lulus" {{ old('hasil') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus (Celah Kritis Tidak Teratasi)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tingkat Risiko Tertinggi Temuan <span class="text-rose-500">*</span>
                    </label>
                    <select name="tingkat_risiko" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="aman" {{ old('tingkat_risiko', 'aman') === 'aman' ? 'selected' : '' }}>Aman (Clean - Tidak ada celah signifikan)</option>
                        <option value="rendah" {{ old('tingkat_risiko') === 'rendah' ? 'selected' : '' }}>Rendah (Low Severity)</option>
                        <option value="sedang" {{ old('tingkat_risiko') === 'sedang' ? 'selected' : '' }}>Sedang (Medium Severity)</option>
                        <option value="tinggi" {{ old('tingkat_risiko') === 'tinggi' ? 'selected' : '' }}>Tinggi (High Severity)</option>
                        <option value="kritis" {{ old('tingkat_risiko') === 'kritis' ? 'selected' : '' }}>Kritis (Critical Severity)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Administrasi & Berkas Laporan -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-blue-700">description</span>
                <h3 class="text-sm font-bold text-slate-900">2. Berkas Laporan Pentest & Rekomendasi Kelaikan</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nomor Surat Rekomendasi / SKLO (Opsional)
                    </label>
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat', '000.5.2/DISKOMINFOTIK-PERSANDIAN/' . date('Y') . '/' . sprintf('%03d', rand(1, 999))) }}" placeholder="contoh: 000.5.2/DISKOMINFOTIK-PERSANDIAN/2026/045" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">
                    <p class="text-[11px] text-slate-400 mt-1">Nomor surat resmi rekomendasi kelaikan keamanan operasi</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Unggah Laporan Hasil Audit (PDF / DOCX / ZIP)
                    </label>
                    <input type="file" name="file_hasil" accept=".pdf,.doc,.docx,.zip,.rar" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0D47A1]">
                    <p class="text-[11px] text-slate-400 mt-1">Maksimal 20 MB (laporan rinci pengujian pentest)</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Temuan Kerentanan & Rekomendasi Mitigasi -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-rose-600">bug_report</span>
                <h3 class="text-sm font-bold text-slate-900">3. Rincian Temuan Celah & Saran Mitigasi</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Daftar Temuan Kerentanan (Vulnerabilities / CVE / OWASP)
                    </label>
                    <textarea name="catatan_kerentanan" rows="4" placeholder="Contoh:&#10;- OWASP A01: Broken Access Control pada endpoint /api/pasien&#10;- OWASP A03: SQL Injection terdeteksi pada parameter filter ID&#10;- Outdated library jQuery 1.11 terdeteksi..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">{{ old('catatan_kerentanan') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Rekomendasi Tindakan Mitigasi Bagi Pengembang
                    </label>
                    <textarea name="rekomendasi" rows="4" placeholder="Contoh:&#10;1. Gunakan parameterized query / Eloquent ORM untuk mencegah SQL injection.&#10;2. Implementasikan middleware validasi kepemilikan data sebelum response dikembalikan.&#10;3. Perbarui library pihak ketiga ke versi mutakhir..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">{{ old('rekomendasi') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Catatan Tambahan Auditor (Opsional)
                    </label>
                    <textarea name="keterangan" rows="2" placeholder="Catatan umum proses pengujian..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">{{ old('keterangan', 'Pengujian kerentanan siber telah selesai dilaksanakan oleh tim CSIRT Riau.') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                <span class="font-bold text-slate-700">Perhatian:</span> Hasil <span class="font-bold text-emerald-700">Lulus</span> otomatis memindahkan proyek ke antrean <span class="font-bold text-blue-700">Deployment</span>. Hasil <span class="font-bold text-amber-700">Perbaikan</span> mengembalikan proyek ke programmer.
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('persandian.pengujian.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition text-center">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm transition">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Simpan & Terbitkan Hasil Audit</span>
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
