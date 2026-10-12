@extends('layouts.admin')

@section('title', 'Edit Syarat Dokumen')
@section('page-title', 'Perbarui Master Dokumen')
@section('breadcrumb', 'Master Dokumen / Edit')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Perbarui Syarat Dokumen</h2>
                <p class="text-xs text-slate-500 mt-1">Ubah ketentuan berkas regulasi: {{ $masterDokumen->nama_dokumen }}</p>
            </div>
            <a href="{{ route('superadmin.master-dokumen.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('superadmin.master-dokumen.update', $masterDokumen) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Dokumen -->
            <div>
                <label for="nama_dokumen" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Dokumen Regulasi <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_dokumen" 
                    id="nama_dokumen" 
                    value="{{ old('nama_dokumen', $masterDokumen->nama_dokumen) }}" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_dokumen') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                    required
                >
                @error('nama_dokumen')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kode Dokumen -->
                <div>
                    <label for="kode_dokumen" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kode Folder & Slug <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="kode_dokumen" 
                        id="kode_dokumen" 
                        value="{{ old('kode_dokumen', $masterDokumen->kode_dokumen) }}" 
                        class="block w-full px-3.5 py-2.5 font-mono bg-slate-50/50 border @error('kode_dokumen') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk nama subfolder isolasi storage berkas.</p>
                    @error('kode_dokumen')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Berlaku Untuk -->
                <div>
                    <label for="berlaku_untuk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kategori Permohonan <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="berlaku_untuk" 
                        id="berlaku_untuk" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('berlaku_untuk') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                        <option value="semua" {{ old('berlaku_untuk', $masterDokumen->berlaku_untuk) === 'semua' ? 'selected' : '' }}>Semua Jenis Permohonan</option>
                        <option value="new_app" {{ old('berlaku_untuk', $masterDokumen->berlaku_untuk) === 'new_app' ? 'selected' : '' }}>Pembangunan Aplikasi Baru</option>
                        <option value="app_development" {{ old('berlaku_untuk', $masterDokumen->berlaku_untuk) === 'app_development' ? 'selected' : '' }}>Pengembangan Aplikasi Lama</option>
                        <option value="app_deactivation" {{ old('berlaku_untuk', $masterDokumen->berlaku_untuk) === 'app_deactivation' ? 'selected' : '' }}>Penonaktifan Aplikasi</option>
                    </select>
                    @error('berlaku_untuk')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Format Diizinkan -->
                <div>
                    <label for="format_diizinkan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Format Ekstensi <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="format_diizinkan" 
                        id="format_diizinkan" 
                        value="{{ old('format_diizinkan', $masterDokumen->format_diizinkan) }}" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('format_diizinkan') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    @error('format_diizinkan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Size KB -->
                <div>
                    <label for="max_size_kb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Batas Ukuran (KB) <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="max_size_kb" 
                        id="max_size_kb" 
                        value="{{ old('max_size_kb', $masterDokumen->max_size_kb) }}" 
                        step="512" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('max_size_kb') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    <p class="text-[11px] text-slate-400 mt-1">{{ number_format($masterDokumen->max_size_kb / 1024, 1) }} MB</p>
                    @error('max_size_kb')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urutan Tampil -->
                <div>
                    <label for="urutan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Urutan Tampil <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="urutan" 
                        id="urutan" 
                        value="{{ old('urutan', $masterDokumen->urutan) }}" 
                        min="1" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('urutan') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    @error('urutan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Upload File Template Blangko (Opsional) -->
            <div>
                <label for="file_template" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Template / Blangko Dokumen Resmi (Opsional)
                </label>
                @if ($masterDokumen->file_template)
                    <div class="mb-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-slate-700 font-medium truncate">
                            <span class="material-symbols-outlined text-[18px] text-emerald-600">description</span>
                            <span class="truncate">Berkas template saat ini: {{ basename($masterDokumen->file_template) }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $masterDokumen->file_template) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold shrink-0">
                            Pratinjau
                        </a>
                    </div>
                @endif
                <input 
                    type="file" 
                    name="file_template" 
                    id="file_template" 
                    accept=".pdf,.doc,.docx,.xls,.xlsx" 
                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0D47A1] hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50/50 p-2"
                >
                <p class="text-[11px] text-slate-400 mt-1">Unggah berkas baru untuk memperbarui blangko yang sudah ada.</p>
                @error('file_template')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi / Petunjuk Pengisian -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Petunjuk Pengisian bagi OPD
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('deskripsi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('deskripsi', $masterDokumen->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkbox Sifat Wajib & Status Aktif -->
            <div class="pt-2 flex flex-col sm:flex-row gap-6 border-t border-slate-100">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="wajib" 
                        value="1" 
                        {{ old('wajib', $masterDokumen->wajib ? '1' : '0') == '1' ? 'checked' : '' }} 
                        class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1] border-slate-300"
                    >
                    <div>
                        <div class="text-sm font-semibold text-slate-800">Dokumen Wajib Dilampirkan</div>
                        <div class="text-xs text-slate-400">Pengajuan tidak dapat dikirim sebelum berkas ini diunggah.</div>
                    </div>
                </label>

                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1" 
                        {{ old('is_active', $masterDokumen->is_active ? '1' : '0') == '1' ? 'checked' : '' }} 
                        class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                    >
                    <div>
                        <div class="text-sm font-semibold text-slate-800">Status Aktif</div>
                        <div class="text-xs text-slate-400">Muncul di formulir permohonan OPD saat ini.</div>
                    </div>
                </label>
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.master-dokumen.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Perbarui Dokumen</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
