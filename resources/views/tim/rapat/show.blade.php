@extends('layouts.tim')

@section('title', 'Detail Agenda Rapat - ' . $rapat->nama_rapat)
@section('page-title', 'Detail Agenda Rapat & Presensi')
@section('breadcrumb', 'Agenda Rapat / Detail')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                    @if ($rapat->status === 'selesai') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif ($rapat->status === 'dibatalkan') bg-rose-50 text-rose-700 border border-rose-200
                    @else bg-blue-50 text-blue-700 border border-blue-200
                    @endif">
                    Status: {{ ucfirst($rapat->status ?? 'Dijadwalkan') }}
                </span>
                <span class="text-xs text-slate-400">
                    Proyek: <strong class="text-slate-700">{{ $rapat->project->nama_project ?? '-' }}</strong>
                </span>
            </div>

            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                {{ $rapat->nama_rapat }}
            </h2>

            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mt-2.5">
                <span class="flex items-center gap-1.5 font-semibold text-slate-800">
                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">calendar_month</span>
                    <span>{{ $rapat->waktu_rapat ? $rapat->waktu_rapat->format('l, d F Y') : '-' }}</span>
                </span>
                <span>•</span>
                <span class="flex items-center gap-1.5 font-mono text-[#0D47A1] font-semibold">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                    <span>{{ $rapat->waktu_rapat ? $rapat->waktu_rapat->format('H:i') . ' WIB' : '-' }}</span>
                </span>
                <span>•</span>
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px] text-slate-400">location_on</span>
                    <span>{{ $rapat->tempat_rapat ?? 'Ruang Rapat APTIKA' }}</span>
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('tim.rapat.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
            @if ($rapat->file_surat_rapat)
                <a href="{{ asset('storage/' . $rapat->file_surat_rapat) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#0D47A1] text-xs font-bold transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">description</span>
                    <span>Surat Undangan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Interactive Presensi Form Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">how_to_reg</span>
                    <span>Konfirmasi Kehadiran Presensi</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Pilih status kehadiran Anda untuk memberikan kepastian koordinasi kepada pimpinan dan tim APTIKA.
                </p>
            </div>
            <div>
                @php
                    $currentStatus = $userPeserta->status_kehadiran ?? null;
                @endphp
                @if (!$currentStatus)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        <span>Belum Dikonfirmasi</span>
                    </span>
                @elseif ($currentStatus === 'hadir')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                        <span>Terkonfirmasi: Hadir</span>
                    </span>
                @elseif ($currentStatus === 'izin')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                        <span class="material-symbols-outlined text-[15px]">info</span>
                        <span>Terkonfirmasi: Izin</span>
                    </span>
                @elseif ($currentStatus === 'tidak_hadir')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="material-symbols-outlined text-[15px]">cancel</span>
                        <span>Terkonfirmasi: Tidak Hadir</span>
                    </span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('tim.rapat.presensi', $rapat) }}" class="mt-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Hadir Option -->
                <label class="cursor-pointer">
                    <input type="radio" name="status_kehadiran" value="hadir" class="peer sr-only" {{ ($currentStatus === 'hadir' || old('status_kehadiran') === 'hadir') ? 'checked' : '' }}>
                    <div class="p-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/40 peer-checked:ring-2 peer-checked:ring-emerald-600/20 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
                            <span class="text-sm font-bold text-slate-900">Hadir</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Saya akan hadir tepat waktu pada agenda rapat ini.
                        </p>
                    </div>
                </label>

                <!-- Izin Option -->
                <label class="cursor-pointer">
                    <input type="radio" name="status_kehadiran" value="izin" class="peer sr-only" {{ ($currentStatus === 'izin' || old('status_kehadiran') === 'izin') ? 'checked' : '' }}>
                    <div class="p-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-sky-600 peer-checked:bg-sky-50/40 peer-checked:ring-2 peer-checked:ring-sky-600/20 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-sky-600">info</span>
                            <span class="text-sm font-bold text-slate-900">Izin</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Izin berhalangan hadir karena ada tugas lain atau kendala teknis.
                        </p>
                    </div>
                </label>

                <!-- Tidak Hadir Option -->
                <label class="cursor-pointer">
                    <input type="radio" name="status_kehadiran" value="tidak_hadir" class="peer sr-only" {{ ($currentStatus === 'tidak_hadir' || old('status_kehadiran') === 'tidak_hadir') ? 'checked' : '' }}>
                    <div class="p-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-rose-600 peer-checked:bg-rose-50/40 peer-checked:ring-2 peer-checked:ring-rose-600/20 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-rose-600">cancel</span>
                            <span class="text-sm font-bold text-slate-900">Tidak Hadir</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Tidak dapat hadir pada agenda pertemuan kali ini.
                        </p>
                    </div>
                </label>
            </div>

            @error('status_kehadiran')
                <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror

            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-sm shadow-[#0D47A1]/20 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Status Presensi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Agenda & Deskripsi -->
    @if ($rapat->deskripsi)
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-2">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-[#0D47A1]">description</span>
                <span>Agenda Pembahasan Rapat</span>
            </h3>
            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-xl border border-slate-200/60 font-sans">
                {{ $rapat->deskripsi }}
            </div>
        </div>
    @endif

    <!-- Notulensi & Dokumentasi Rapat (Jika ada) -->
    @if ($rapat->notulensi || $rapat->file_dokumentasi)
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-emerald-600">history_edu</span>
                    <span>Hasil & Notulensi Rapat</span>
                </h3>
                @if ($rapat->file_dokumentasi)
                    <a href="{{ asset('storage/' . $rapat->file_dokumentasi) }}" target="_blank" class="text-xs font-bold text-[#0D47A1] hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">attachment</span>
                        <span>Unduh Berkas Dokumentasi</span>
                    </a>
                @endif
            </div>

            @if ($rapat->notulensi)
                <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-emerald-50/30 p-4 rounded-xl border border-emerald-100 font-sans">
                    {{ $rapat->notulensi }}
                </div>
            @endif
        </div>
    @endif

    <!-- Daftar Rekan & Peserta Terhubung -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">groups</span>
                    <span>Daftar Peserta Rapat Terdaftar</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Personil tim teknis, perwakilan APTIKA, dan pihak terkait</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#0D47A1] border border-blue-200">
                {{ $rapat->peserta->count() }} Peserta
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($rapat->peserta as $p)
                <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50/50 transition">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 uppercase border border-slate-200">
                            {{ substr($p->user->name ?? 'U', 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ $p->user->name ?? '-' }}</h4>
                                @if ($p->user_id === auth()->id())
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-[#0D47A1]">Anda</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                {{ $p->user->jabatan ?? ($p->user->roles->first()->name ?? 'Anggota Tim') }}
                            </div>
                        </div>
                    </div>

                    <div>
                        @if ($p->status_kehadiran === 'hadir')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                <span>Hadir</span>
                            </span>
                        @elseif ($p->status_kehadiran === 'izin')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span>Izin</span>
                            </span>
                        @elseif ($p->status_kehadiran === 'tidak_hadir')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="material-symbols-outlined text-[14px]">cancel</span>
                                <span>Tidak Hadir</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="material-symbols-outlined text-[14px]">pending</span>
                                <span>Menunggu</span>
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    Belum ada peserta yang didaftarkan dalam rapat ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
