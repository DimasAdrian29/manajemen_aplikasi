@extends('layouts.aptika')

@section('title', 'Tambah Layanan Baru')
@section('page-title', 'Tambah Layanan Fasilitasi')
@section('breadcrumb', 'Konfigurasi Alur Layanan / Daftar Layanan / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Formulir Layanan Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan nama, identitas visual, dan deskripsi layanan fasilitasi.</p>
            </div>
            <a href="{{ route('aptika.layanan.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </a>
        </div>

        <form method="POST" action="{{ route('aptika.layanan.store') }}" class="p-6 space-y-5">
            @csrf

            <!-- Nama Layanan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Layanan <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    value="{{ old('nama') }}" 
                    required 
                    placeholder="Contoh: Pembangunan Aplikasi Baru, Penyesuaian Modul..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                @error('nama')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi Layanan
                </label>
                <textarea 
                    name="deskripsi" 
                    rows="3" 
                    placeholder="Penjelasan singkat mengenai peruntukan layanan ini bagi OPD..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ikon & Warna Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Ikon Material Symbols -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Ikon (Material Symbols)
                    </label>
                    <input 
                        type="text" 
                        id="iconInput"
                        name="ikon" 
                        value="{{ old('ikon', 'category') }}" 
                        placeholder="category, add_to_photos, dll" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    <div class="flex items-center gap-1.5 mt-2 flex-wrap text-xs text-slate-400">
                        <span>Pilihan cepat:</span>
                        <button type="button" onclick="document.getElementById('iconInput').value='add_to_photos'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">add_to_photos</button>
                        <button type="button" onclick="document.getElementById('iconInput').value='published_with_changes'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">published_with_changes</button>
                        <button type="button" onclick="document.getElementById('iconInput').value='power_settings_new'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">power_settings_new</button>
                        <button type="button" onclick="document.getElementById('iconInput').value='rocket_launch'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">rocket_launch</button>
                    </div>
                </div>

                <!-- Warna Tema -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Warna Aksen Layanan
                    </label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="color" 
                            id="colorPicker"
                            name="warna" 
                            value="{{ old('warna', '#0D47A1') }}" 
                            class="w-10 h-10 p-0.5 rounded-xl border border-slate-200 cursor-pointer"
                        >
                        <input 
                            type="text" 
                            id="colorInput"
                            value="{{ old('warna', '#0D47A1') }}" 
                            oninput="document.getElementById('colorPicker').value=this.value"
                            class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono text-slate-800"
                        >
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                        <button type="button" onclick="setColor('#10B981')" class="w-5 h-5 rounded-full bg-emerald-500 border border-white shadow-xs"></button>
                        <button type="button" onclick="setColor('#0D47A1')" class="w-5 h-5 rounded-full bg-blue-800 border border-white shadow-xs"></button>
                        <button type="button" onclick="setColor('#F59E0B')" class="w-5 h-5 rounded-full bg-amber-500 border border-white shadow-xs"></button>
                        <button type="button" onclick="setColor('#8B5CF6')" class="w-5 h-5 rounded-full bg-purple-500 border border-white shadow-xs"></button>
                        <button type="button" onclick="setColor('#E11D48')" class="w-5 h-5 rounded-full bg-rose-500 border border-white shadow-xs"></button>
                    </div>
                </div>
            </div>

            <!-- Urutan & Status Aktif -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Urut Tampilan
                    </label>
                    <input 
                        type="number" 
                        name="urutan" 
                        value="{{ old('urutan', $nextUrutan) }}" 
                        min="1" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                </div>

                <div class="space-y-3 pt-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1]">
                        <span class="text-xs font-semibold text-slate-700">Aktifkan Layanan Segera</span>
                    </label>

                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="memerlukan_aplikasi_eksisting" value="1" {{ old('memerlukan_aplikasi_eksisting') ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded text-[#0D47A1] focus:ring-[#0D47A1]">
                        <div>
                            <span class="text-xs font-semibold text-slate-700 block">Memerlukan Aplikasi Eksisting</span>
                            <span class="text-[11px] text-slate-500 block">Centang jika usulan ini ditujukan untuk aplikasi yang telah ada (pemohon wajib memilih dari daftar aplikasi instansi).</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('aptika.layanan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition">
                    Simpan & Lanjut Susun Alur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function setColor(color) {
        document.getElementById('colorPicker').value = color;
        document.getElementById('colorInput').value = color;
    }
    document.getElementById('colorPicker').addEventListener('input', function(e) {
        document.getElementById('colorInput').value = e.target.value;
    });
</script>
@endsection
