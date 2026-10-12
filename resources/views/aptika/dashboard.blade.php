@extends('layouts.aptika')

@section('title', 'Dashboard APTIKA')
@section('page-title', 'Dashboard Bidang APTIKA')
@section('breadcrumb', 'Pusat Telaah Permohonan, Inisiasi Proyek & Kendali Mutu SPBE')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0D47A1] via-[#1565C0] to-[#1E88E5] p-6 sm:p-8 text-white shadow-lg shadow-[#0D47A1]/15">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-xs mb-3 border border-white/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Pusat Kendali Aplikasi Informatika (APTIKA)</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                Halo, {{ auth()->user()->name }}
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                Kelola tinjauan proposal teknis perangkat daerah, bentuk susunan tim pengembangan internal, jadwalkan rapat koordinasi terpadu, dan laksanakan audit uji fungsional sistem.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('aptika.pengajuan.index', ['status' => 'submitted']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#0D47A1] text-xs font-bold hover:bg-blue-50 shadow-sm transition">
                    <span class="material-symbols-outlined text-[18px]">rate_review</span>
                    <span>Telaah Permohonan OPD</span>
                </a>
                <a href="{{ route('aptika.project.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 backdrop-blur-xs transition">
                    <span class="material-symbols-outlined text-[18px]">assignment_add</span>
                    <span>Inisiasi Proyek Baru</span>
                </a>
                <a href="{{ route('aptika.rapat.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 backdrop-blur-xs transition">
                    <span class="material-symbols-outlined text-[18px]">add_call</span>
                    <span>Jadwalkan Rapat</span>
                </a>
            </div>
        </div>
        <!-- Decorative Background Icon -->
        <div class="absolute -right-6 -bottom-6 text-white/10 pointer-events-none select-none">
            <span class="material-symbols-outlined text-[210px]">devices</span>
        </div>
    </div>

    <!-- Stat Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Menunggu Telaah -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Menunggu Telaah</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">hourglass_top</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-600 mt-2">{{ $stats['menunggu_telaah'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Perlu verifikasi awal</div>
        </div>

        <!-- Sedang Ditelaah -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Dalam Telaah</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0D47A1] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#0D47A1] mt-2">{{ $stats['sedang_ditelaah'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Sedang dikaji teknis</div>
        </div>

        <!-- Proyek Aktif -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Proyek Berjalan</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">code</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['proyek_aktif'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Inisiasi & Coding</div>
        </div>

        <!-- Uji Fungsional -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Uji Fungsional</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">fact_check</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600 mt-2">{{ $stats['uji_fungsional'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Audit fitur & bug</div>
        </div>

        <!-- Rapat Mendatang -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Agenda Rapat</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">event</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['rapat_mendatang'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Koordinasi mendatang</div>
        </div>
    </div>

    <!-- Section: Permohonan Usulan Menunggu Telaah -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">rate_review</span>
                    <span>Usulan OPD Menunggu Telaah & Keputusan Teknis</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kaji kelayakan KAK, regulasi, dan putuskan persetujuan layanan</p>
            </div>
            <a href="{{ route('aptika.pengajuan.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                <span>Lihat Semua ({{ $stats['total_pengajuan'] }})</span>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">No. Registrasi</th>
                        <th class="py-3.5 px-6">Perangkat Daerah (OPD)</th>
                        <th class="py-3.5 px-6">Layanan & Nama Usulan</th>
                        <th class="py-3.5 px-6 text-center">Berkas</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($pengajuanPending as $p)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-[#0D47A1]">
                                {{ $p->kode_pengajuan }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $p->instansi->nama_instansi }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-slate-900">
                                    {{ $p->nama_aplikasi ?? ($p->aplikasi->nama_aplikasi ?? '-') }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    @if ($p->jenis_pengajuan === 'new_app')
                                        Pembangunan Aplikasi Baru
                                    @elseif ($p->jenis_pengajuan === 'app_development')
                                        Pengembangan Modul
                                    @else
                                        Penonaktifan
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    <span class="material-symbols-outlined text-[13px]">attach_file</span>
                                    <span>{{ $p->dokumen->count() }} file</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($p->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Baru Masuk</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Dalam Telaah</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('aptika.pengajuan.show', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#165eae] shadow-xs transition">
                                    <span class="material-symbols-outlined text-[15px]">rate_review</span>
                                    <span>Telaah Berkas</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-1">done_all</span>
                                <p class="text-sm font-semibold">Semua permohonan OPD telah ditelaah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Proyek Berjalan & Agenda Rapat (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Proyek Aktif (2 Kolom) -->
        <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-purple-600">developer_board</span>
                        <span>Proyek Pengembangan Terkini</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pemantauan progres dan tahapan sprint pengembangan tim</p>
                </div>
                <a href="{{ route('aptika.project.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($proyekTerkini as $project)
                    <div class="p-5 hover:bg-slate-50/50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('aptika.project.show', $project) }}" class="text-sm font-bold text-slate-900 hover:text-[#0D47A1] transition">
                                    {{ $project->nama_project }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    @if ($project->status === 'initiated') bg-slate-100 text-slate-700
                                    @elseif ($project->status === 'in_development') bg-purple-50 text-purple-700 border border-purple-200
                                    @elseif ($project->status === 'functional_testing') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @else bg-blue-50 text-blue-700 border border-blue-200
                                    @endif">
                                    {{ str_replace('_', ' ', $project->status) }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500">
                                Instansi: <strong>{{ $project->aplikasi->instansi->nama_instansi ?? '-' }}</strong>
                            </div>
                            <div class="text-[11px] text-slate-400">
                                Tim: {{ $project->tim->count() }} orang • Ketua: {{ $project->tim->firstWhere('peran', 'ketua_tim')->user->name ?? 'Belum ditentukan' }}
                            </div>
                        </div>

                        <div class="sm:text-right shrink-0">
                            <div class="text-xs font-bold text-slate-700 mb-1">
                                Progres: {{ $project->progres_total }}%
                            </div>
                            <div class="w-36 bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#0D47A1] h-2 rounded-full" style="width: {{ $project->progres_total }}%"></div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('aptika.project.show', $project) }}" class="text-xs font-semibold text-[#0D47A1] hover:underline">
                                    Kelola Proyek →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400">
                        <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-1">assignment_late</span>
                        <p class="text-sm font-semibold">Belum ada proyek yang sedang berjalan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Agenda Rapat Terdekat (1 Kolom) -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-rose-600">groups</span>
                            <span>Rapat Koordinasi</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal rapat proyek terdekat</p>
                    </div>
                    <a href="{{ route('aptika.rapat.create') }}" class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#0D47A1] transition" title="Jadwalkan Rapat">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                    </a>
                </div>

                <div class="divide-y divide-slate-100 p-2">
                    @forelse ($rapatMendatang as $rapat)
                        <div class="p-4 rounded-xl hover:bg-slate-50/60 transition space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    {{ $rapat->waktu_rapat->format('d M Y, H:i') }} WIB
                                </span>
                                <span class="text-[11px] font-medium text-slate-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">group</span>
                                    <span>{{ $rapat->peserta->count() }}</span>
                                </span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 line-clamp-1">
                                {{ $rapat->nama_rapat }}
                            </h4>
                            <div class="text-[11px] text-slate-500 truncate">
                                Proyek: {{ $rapat->project->nama_project ?? '-' }}
                            </div>
                            <div class="text-[11px] text-slate-400 flex items-center gap-1 truncate">
                                <span class="material-symbols-outlined text-[13px]">location_on</span>
                                <span>{{ $rapat->tempat_rapat }}</span>
                            </div>
                            <div class="pt-1">
                                <a href="{{ route('aptika.rapat.show', $rapat) }}" class="text-xs font-bold text-[#0D47A1] hover:underline flex items-center gap-1">
                                    <span>Buka Lembar Rapat</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-[32px] text-slate-300 block mb-1">event_available</span>
                            <p class="text-xs font-semibold">Tidak ada rapat terjadwal dalam waktu dekat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 text-center">
                <a href="{{ route('aptika.rapat.index') }}" class="text-xs font-bold text-[#0D47A1] hover:underline">
                    Lihat Semua Arsip Rapat →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
