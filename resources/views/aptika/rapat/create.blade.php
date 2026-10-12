@extends('layouts.aptika')

@section('title', 'Jadwalkan Rapat Proyek')
@section('page-title', 'Formulir Penjadwalan Rapat Proyek')
@section('breadcrumb', 'Agenda Rapat / Jadwalkan Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Jadwalkan Rapat Koordinasi Proyek</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Hubungkan tim teknis internal Kominfo dan instansi OPD pemohon dalam satu forum rapat. Notifikasi otomatis akan dikirimkan ke akun seluruh peserta.
                </p>
            </div>
            <a href="{{ route('aptika.rapat.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('aptika.rapat.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. Pilih Proyek Terkait -->
            <div>
                <label for="project_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Proyek Aplikasi <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="project_id" 
                    id="project_id" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('project_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                    <option value="">-- Pilih Proyek Terkait --</option>
                    @foreach ($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id', request('project_id')) == $proj->id ? 'selected' : '' }}>
                            {{ $proj->nama_project }} ({{ $proj->aplikasi->instansi->nama_instansi ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Nama Rapat / Agenda -->
            <div>
                <label for="nama_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama / Judul Rapat <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_rapat" 
                    id="nama_rapat" 
                    value="{{ old('nama_rapat') }}" 
                    placeholder="Contoh: Rapat Koordinasi Kickoff & Penyelarasan KAK Modul E-Puskesmas" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_rapat') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                @error('nama_rapat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Waktu & Tempat Rapat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="waktu_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Waktu & Jam Pelaksanaan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        name="waktu_rapat" 
                        id="waktu_rapat" 
                        value="{{ old('waktu_rapat', date('Y-m-d\TH:i', strtotime('+1 day 09:00'))) }}" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('waktu_rapat') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    @error('waktu_rapat')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tempat_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tempat Pelaksanaan / Tautan Video Call
                    </label>
                    <input 
                        type="text" 
                        name="tempat_rapat" 
                        id="tempat_rapat" 
                        value="{{ old('tempat_rapat', 'Ruang Rapat Bidang APTIKA Lt. 2 Diskominfo Riau') }}" 
                        placeholder="Ruang Rapat APTIKA Lt. 2 / Link Zoom Meeting" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('tempat_rapat') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    @error('tempat_rapat')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 4. Deskripsi & Pokok Bahasan -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi & Agenda Pembahasan
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3" 
                    placeholder="Tuliskan poin-poin agenda rapat yang akan dibahas bersama tim pengembang dan pihak instansi pemohon..." 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('deskripsi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Upload Berkas Surat Undangan Rapat (PDF) -->
            <div>
                <label for="file_surat_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Surat Undangan Resmi Rapat (PDF / DOCX)
                </label>
                <input 
                    type="file" 
                    name="file_surat_rapat" 
                    id="file_surat_rapat" 
                    accept=".pdf,.doc,.docx" 
                    class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0D47A1] hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50/50 p-2"
                >
                <p class="text-[11px] text-slate-400 mt-1">Berkas surat undangan bertandatangan Kepala Dinas atau Kepala Bidang.</p>
                @error('file_surat_rapat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 6. Hubungkan Peserta Rapat (Tim Kominfo + Pihak OPD) -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Hubungkan Peserta Rapat <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400">Pilih tim internal Kominfo dan perwakilan OPD yang diundang dalam rapat ini.</p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                        <span class="material-symbols-outlined text-[14px]">notifications_active</span>
                        <span>Auto-Notifikasi Terkirim</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto p-3 rounded-xl border border-slate-200 bg-slate-50/40">
                    @foreach ($allUsers as $u)
                        <label class="flex items-center gap-3 p-2.5 rounded-lg bg-white border border-slate-200/80 hover:border-slate-300 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="peserta_ids[]" 
                                value="{{ $u->id }}" 
                                {{ in_array($u->id, old('peserta_ids', [])) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1] border-slate-300"
                            >
                            <div class="text-xs overflow-hidden">
                                <div class="font-bold text-slate-900 truncate">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-500 truncate">
                                    {{ $u->roles->first()->name ?? '-' }} • {{ $u->instansi->nama_instansi ?? 'Diskominfo Riau' }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('peserta_ids')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('aptika.rapat.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-bold shadow-md shadow-[#0D47A1]/20 transition flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Jadwalkan & Kirim Undangan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
