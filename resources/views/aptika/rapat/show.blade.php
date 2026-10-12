@extends('layouts.aptika')

@section('title', 'Lembar Rapat ' . $rapat->nama_rapat)
@section('page-title', 'Detail Lembar Rapat Koordinasi')
@section('breadcrumb', 'Agenda Rapat / ' . $rapat->nama_rapat)

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                    @if ($rapat->status === 'selesai') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif ($rapat->status === 'dibatalkan') bg-rose-50 text-rose-700 border border-rose-200
                    @else bg-blue-50 text-blue-700 border border-blue-200
                    @endif">
                    Status: {{ ucfirst($rapat->status) }}
                </span>
                <span class="text-xs text-slate-400">
                    Proyek: <strong class="text-slate-700">{{ $rapat->project->nama_project ?? '-' }}</strong>
                </span>
            </div>

            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                {{ $rapat->nama_rapat }}
            </h2>

            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mt-2">
                <span class="flex items-center gap-1 font-semibold text-slate-800">
                    <span class="material-symbols-outlined text-[16px] text-[#0D47A1]">schedule</span>
                    <span>{{ $rapat->waktu_rapat->format('l, d F Y - H:i') }} WIB</span>
                </span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">location_on</span>
                    <span>{{ $rapat->tempat_rapat }}</span>
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('aptika.rapat.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Daftar Rapat</span>
            </a>
            @if ($rapat->file_surat_rapat)
                <a href="{{ asset('storage/' . $rapat->file_surat_rapat) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#0D47A1] text-xs font-bold transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">description</span>
                    <span>Surat Undangan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Agenda & Deskripsi -->
    @if ($rapat->deskripsi)
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-2">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Agenda Pembahasan Rapat</h3>
            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-xl border border-slate-200/60">
                {{ $rapat->deskripsi }}
            </div>
        </div>
    @endif

    <!-- Daftar Peserta Terhubung & Presensi -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-purple-600">groups</span>
                    <span>Daftar Peserta Rapat Terhubung</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Seluruh personil tim teknis Kominfo dan pihak OPD yang diundang</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                {{ $rapat->peserta->count() }} Peserta
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($rapat->peserta as $p)
                <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50/50 transition">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                            {{ substr($p->user->name ?? 'U', 0, 2) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">{{ $p->user->name }}</h4>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $p->user->roles->first()->name ?? '-' }} • {{ $p->user->instansi->nama_instansi ?? 'Diskominfo Riau' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            @if ($p->status_kehadiran === 'hadir') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif ($p->status_kehadiran === 'izin') bg-amber-50 text-amber-700 border border-amber-200
                            @elseif ($p->status_kehadiran === 'tidak_hadir') bg-rose-50 text-rose-700 border border-rose-200
                            @else bg-slate-100 text-slate-600 border border-slate-200
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $p->status_kehadiran)) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Tidak ada data peserta terhubung.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Notulensi & Dokumentasi Hasil Rapat -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">edit_note</span>
                <span>Notulensi & Dokumentasi Pelaksanaan Rapat</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Catat hasil kesepakatan rapat, tindak lanjut, dan unggah foto/arsip dokumentasi.</p>
        </div>

        <form method="POST" action="{{ route('aptika.rapat.notulensi', $rapat) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Notulensi Textarea -->
            <div>
                <label for="notulensi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Hasil Notulensi Rapat <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    name="notulensi" 
                    id="notulensi" 
                    rows="6" 
                    placeholder="Tuliskan butir-butir kesepakatan rapat, pembagian tugas tambahan, dan batas waktu tindak lanjut..." 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('notulensi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >{{ old('notulensi', $rapat->notulensi) }}</textarea>
                @error('notulensi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload File Dokumentasi -->
            <div>
                <label for="file_dokumentasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Unggah Foto Dokumentasi / Berita Acara Rapat
                </label>
                @if ($rapat->file_dokumentasi)
                    <div class="mb-3 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-slate-700 font-semibold truncate">
                            <span class="material-symbols-outlined text-[18px] text-emerald-600">attachment</span>
                            <span class="truncate">Dokumentasi tersimpan: {{ basename($rapat->file_dokumentasi) }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $rapat->file_dokumentasi) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold shrink-0">
                            Lihat Berkas
                        </a>
                    </div>
                @endif
                <input 
                    type="file" 
                    name="file_dokumentasi" 
                    id="file_dokumentasi" 
                    accept=".pdf,.jpg,.jpeg,.png,.zip" 
                    class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0D47A1] hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50/50 p-2"
                >
                @error('file_dokumentasi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Rapat -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Status Rapat
                </label>
                <select 
                    name="status" 
                    id="status" 
                    class="block w-full sm:w-64 px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                    <option value="dijadwalkan" {{ $rapat->status === 'dijadwalkan' ? 'selected' : '' }}>Masih Dijadwalkan</option>
                    <option value="selesai" {{ $rapat->status === 'selesai' ? 'selected' : '' }}>Rapat Telah Selesai</option>
                    <option value="dibatalkan" {{ $rapat->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-bold shadow-md shadow-[#0D47A1]/20 transition flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Notulensi & Dokumentasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
