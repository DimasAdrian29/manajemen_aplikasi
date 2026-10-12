@extends('layouts.tim')

@section('title', 'Agenda Rapat & Koordinasi')
@section('page-title', 'Agenda Rapat & Koordinasi Tim')
@section('breadcrumb', 'Agenda Rapat')

@section('content')
<div class="space-y-6">

    <!-- Header Description Card -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0D47A1] via-[#1565C0] to-slate-900 p-6 sm:p-7 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-white/10 text-blue-100 border border-white/20">
                    Siklus Koordinasi
                </span>
                <span class="text-xs text-blue-200">Agenda Tatap Muka & Daring</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Rapat Proyek & Tim Pengembang</h2>
            <p class="text-xs sm:text-sm text-blue-100/80 mt-1 max-w-2xl leading-relaxed">
                Daftar undangan rapat sprint, evaluasi teknis APTIKA, dan koordinasi keamanan CSIRT. Harap konfirmasikan status presensi kehadiran Anda sebelum agenda dimulai.
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-2 bg-white/10 backdrop-blur-xs px-4 py-3 rounded-xl border border-white/15">
            <span class="material-symbols-outlined text-blue-200 text-[28px]">calendar_clock</span>
            <div>
                <div class="text-[10px] uppercase font-bold text-blue-200">Total Undangan</div>
                <div class="text-lg font-black text-white">{{ $rapats->total() }} Agenda</div>
            </div>
        </div>
    </div>

    <!-- Data List Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">event_note</span>
                <span>Jadwal Agenda Rapat</span>
            </h3>
            <span class="text-xs text-slate-400 font-medium">Halaman {{ $rapats->currentPage() }} dari {{ $rapats->lastPage() }}</span>
        </div>

        @if ($rapats->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#0D47A1] flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[30px]">event_busy</span>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada Agenda Rapat</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 leading-relaxed">
                    Anda belum terdaftar dalam agenda rapat proyek saat ini. Setiap rapat baru yang dijadwalkan oleh Tim Mutu APTIKA akan tampil di halaman ini.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Waktu & Tanggal</th>
                            <th class="py-3.5 px-6">Nama Agenda Rapat</th>
                            <th class="py-3.5 px-6">Proyek Terkait</th>
                            <th class="py-3.5 px-6">Tempat / Ruang</th>
                            <th class="py-3.5 px-6 text-center">Presensi Saya</th>
                            <th class="py-3.5 px-6 text-center">Status Rapat</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($rapats as $rapat)
                            @php
                                $myPresensi = $rapat->peserta->first();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6 text-xs whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ $rapat->waktu_rapat ? $rapat->waktu_rapat->format('d M Y') : '-' }}</div>
                                    <div class="text-slate-400 font-mono mt-0.5">{{ $rapat->waktu_rapat ? $rapat->waktu_rapat->format('H:i') . ' WIB' : '-' }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <a href="{{ route('tim.rapat.show', $rapat) }}" class="font-bold text-slate-900 hover:text-[#0D47A1] transition block">
                                        {{ $rapat->nama_rapat }}
                                    </a>
                                    @if ($rapat->deskripsi)
                                        <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5" title="{{ $rapat->deskripsi }}">
                                            {{ $rapat->deskripsi }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <div class="font-semibold text-slate-800">{{ $rapat->project->nama_project ?? '-' }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $rapat->project->aplikasi->nama_aplikasi ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-slate-400">location_on</span>
                                        <span>{{ $rapat->tempat_rapat ?? 'Ruang Rapat APTIKA' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if (!$myPresensi || empty($myPresensi->status_kehadiran))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                            <span>Belum Konfirmasi</span>
                                        </span>
                                    @elseif ($myPresensi->status_kehadiran === 'hadir')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            <span>Hadir</span>
                                        </span>
                                    @elseif ($myPresensi->status_kehadiran === 'izin')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                            <span class="material-symbols-outlined text-[14px]">info</span>
                                            <span>Izin</span>
                                        </span>
                                    @elseif ($myPresensi->status_kehadiran === 'tidak_hadir')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                                            <span>Tidak Hadir</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if ($rapat->status === 'dijadwalkan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span>Dijadwalkan</span>
                                        </span>
                                    @elseif ($rapat->status === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-[14px]">task_alt</span>
                                            <span>Selesai</span>
                                        </span>
                                    @elseif ($rapat->status === 'dibatalkan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                            <span>Dibatalkan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ ucfirst($rapat->status ?? '-') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <a href="{{ route('tim.rapat.show', $rapat) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#0D47A1] text-xs font-semibold transition">
                                        <span>Buka & Presensi</span>
                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($rapats->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $rapats->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
