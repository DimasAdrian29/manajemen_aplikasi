@extends('layouts.admin')

@section('title', 'Tambah Instansi OPD')
@section('page-title', 'Tambah Instansi OPD Baru')
@section('breadcrumb', 'Master Data / Instansi OPD / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Formulir Instansi OPD</h2>
                <p class="text-xs text-slate-500 mt-1">Daftarkan badan, dinas, atau perangkat daerah baru Provinsi Riau.</p>
            </div>
            <a href="{{ route('superadmin.instansi.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('superadmin.instansi.store') }}" class="space-y-5">
            @csrf

            <!-- Nama Instansi -->
            <div>
                <label for="nama_instansi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Instansi OPD <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_instansi" 
                    id="nama_instansi" 
                    value="{{ old('nama_instansi') }}" 
                    placeholder="Contoh: Dinas Kesehatan Provinsi Riau" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_instansi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                    required
                >
                @error('nama_instansi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Email Resmi
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        placeholder="dinkes@riau.go.id" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Telepon -->
                <div>
                    <label for="no_telepon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Telepon
                    </label>
                    <input 
                        type="text" 
                        name="no_telepon" 
                        id="no_telepon" 
                        value="{{ old('no_telepon') }}" 
                        placeholder="0761-xxxxxx" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('no_telepon') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    @error('no_telepon')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Alamat Kantor
                </label>
                <textarea 
                    name="alamat" 
                    id="alamat" 
                    rows="3" 
                    placeholder="Jl. Jend. Sudirman No..." 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('alamat') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.instansi.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Instansi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
