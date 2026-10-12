@extends('layouts.admin')

@section('title', 'Daftarkan Aplikasi Baru')
@section('page-title', 'Daftarkan Aplikasi Baru')
@section('breadcrumb', 'Master Data / Aplikasi / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Formulir Entri Aplikasi</h2>
                <p class="text-xs text-slate-500 mt-1">Daftarkan aplikasi milik OPD ke dalam direktori SIMBANGDA.</p>
            </div>
            <a href="{{ route('superadmin.aplikasi.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('superadmin.aplikasi.store') }}" class="space-y-5">
            @csrf

            <!-- Instansi OPD Pemilik -->
            <div>
                <label for="instansi_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Instansi OPD Pemilik <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="instansi_id" 
                    id="instansi_id" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('instansi_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer" 
                    required
                >
                    <option value="">-- Pilih Instansi OPD --</option>
                    @foreach ($instansis as $ins)
                        <option value="{{ $ins->id }}" {{ old('instansi_id') == $ins->id ? 'selected' : '' }}>
                            {{ $ins->nama_instansi }}
                        </option>
                    @endforeach
                </select>
                @error('instansi_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Aplikasi -->
            <div>
                <label for="nama_aplikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Aplikasi <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_aplikasi" 
                    id="nama_aplikasi" 
                    value="{{ old('nama_aplikasi') }}" 
                    placeholder="Contoh: SIMPEG Riau, e-Puskesmas" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_aplikasi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                    required
                >
                @error('nama_aplikasi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- URL Aplikasi -->
            <div>
                <label for="url_aplikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    URL / Tautan Aplikasi (Hosting)
                </label>
                <input 
                    type="url" 
                    name="url_aplikasi" 
                    id="url_aplikasi" 
                    value="{{ old('url_aplikasi') }}" 
                    placeholder="https://aplikasi.riau.go.id" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('url_aplikasi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                <p class="text-[11px] text-slate-400 mt-1">*Dapat dikosongkan apabila aplikasi masih dalam tahap pengembangan.</p>
                @error('url_aplikasi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Operasional -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Status Operasional <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="status" 
                    id="status" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('status') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer" 
                    required
                >
                    <option value="pengembangan" {{ old('status') === 'pengembangan' ? 'selected' : '' }}>Pengembangan (In Development)</option>
                    <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif / Produksi (Live)</option>
                    <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Inactive / Decommissioned)</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi &amp; Fungsi Aplikasi
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3" 
                    placeholder="Jelaskan tujuan dan fungsi utama aplikasi..." 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('deskripsi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.aplikasi.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Aplikasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
