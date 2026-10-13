@extends('layouts.aptika')

@section('title', 'Input Hasil Uji Fungsional')
@section('page-title', 'Pencatatan Hasil Uji Fungsional')
@section('breadcrumb', 'Uji Fungsional / Input Hasil')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Form Hasil Uji Fungsional Aplikasi</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Catat temuan audit kelayakan fungsional sistem sebelum diteruskan ke Bidang Persandian.
                </p>
            </div>
            <a href="{{ route('aptika.testing.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('aptika.testing.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. Pilih Proyek -->
            <div>
                <label for="project_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Proyek Aplikasi <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="project_id" 
                    id="project_id" 
                    onchange="if(this.value) window.location.href='{{ route('aptika.testing.create') }}?project_id=' + this.value"
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('project_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                    <option value="">-- Pilih Proyek yang Diuji --</option>
                    @foreach ($projects as $proj)
                        <option value="{{ $proj->id }}" {{ (old('project_id', $selectedProject?->id ?? request('project_id')) == $proj->id) ? 'selected' : '' }}>
                            {{ $proj->nama_project }} ({{ $proj->aplikasi->instansi->nama_instansi ?? '-' }}) - Status: {{ str_replace('_', ' ', $proj->status) }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Tanggal Pengujian -->
            <div>
                <label for="tanggal_testing" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tanggal Pelaksanaan Uji <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="date" 
                    name="tanggal_testing" 
                    id="tanggal_testing" 
                    value="{{ old('tanggal_testing', date('Y-m-d')) }}" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('tanggal_testing') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                @error('tanggal_testing')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Hasil Status Validasi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keputusan Status Validasi Fungsional <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Lulus -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition select-none flex flex-col justify-between hover:border-emerald-400">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                ✓
                            </span>
                            <input type="radio" name="status_validasi" value="lulus" {{ old('status_validasi') === 'lulus' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <div class="text-xs font-bold text-emerald-800">Lulus Uji Fungsional</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Otomatis maju ke tahap Uji Keamanan (Persandian).</div>
                    </label>

                    <!-- Perbaikan -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition select-none flex flex-col justify-between hover:border-amber-400">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-6 h-6 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                                ↺
                            </span>
                            <input type="radio" name="status_validasi" value="perbaikan" {{ old('status_validasi') === 'perbaikan' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                        </div>
                        <div class="text-xs font-bold text-amber-800">Perbaikan Bug (Revisi)</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Dikembalikan ke Tim Pengembang untuk revisi kode.</div>
                    </label>

                    <!-- Tidak Lulus -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition select-none flex flex-col justify-between hover:border-rose-400">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-6 h-6 rounded-md bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">
                                ✕
                            </span>
                            <input type="radio" name="status_validasi" value="tidak_lulus" {{ old('status_validasi') === 'tidak_lulus' ? 'checked' : '' }} class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                        </div>
                        <div class="text-xs font-bold text-rose-800">Tidak Lulus</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Alur aplikasi tidak sesuai KAK atau arsitektur salah fatal.</div>
                    </label>
                </div>
                @error('status_validasi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Catatan Validasi & Bug List -->
            <div>
                <label for="catatan_validasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Audit & Rincian Temuan Bug
                </label>
                <textarea 
                    name="catatan_validasi" 
                    id="catatan_validasi" 
                    rows="4" 
                    placeholder="Tuliskan temuan modul yang error, validasi input yang bocor, atau rekomendasi perbaikan tampilan bagi tim..." 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('catatan_validasi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('catatan_validasi') }}</textarea>
                @error('catatan_validasi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Berkas Laporan Hasil Uji (Opsional) -->
            <div>
                <label for="file_hasil" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Unggah Dokumen Laporan Pengujian (PDF / DOCX / ZIP)
                </label>
                <input 
                    type="file" 
                    name="file_hasil" 
                    id="file_hasil" 
                    accept=".pdf,.doc,.docx,.zip,.rar,.xlsx" 
                    class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0D47A1] hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50/50 p-2"
                >
                @error('file_hasil')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if($selectedProject && $selectedProject->currentWorkflow)
                <!-- 6. Parameter Dinamis Master Workflow -->
                <div>
                    <x-workflow-step-fields :workflow="$selectedProject->currentWorkflow" />
                </div>
            @endif

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('aptika.testing.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Simpan Hasil Uji Fungsional</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
