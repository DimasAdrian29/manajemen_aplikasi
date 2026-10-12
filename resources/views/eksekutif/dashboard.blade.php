@extends('layouts.eksekutif')

@section('title', 'Executive Dashboard')
@section('page-title', 'Executive Dashboard Pimpinan')
@section('breadcrumb', 'Overview')

@section('content')
<div class="space-y-8">

    <!-- Executive Welcome Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0D47A1] via-[#1565C0] to-slate-900 p-6 sm:p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 border border-blue-900/40">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-white/20 text-white border border-white/20">
                    {{ $isKadis ? 'Level Pimpinan Tertinggi Daerah' : 'Level Pimpinan Bidang Teknis' }}
                </span>
                <span class="text-xs text-blue-100 font-medium">SIMBANGDA Provinsi Riau</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}
            </h2>
            <p class="text-xs sm:text-sm text-blue-100/90 max-w-2xl leading-relaxed">
                @if ($isKadis)
                    Ringkasan makro portofolio digital, kesiapan tata kelola SPBE, dan sebaran sistem aplikasi seluruh Perangkat Daerah Pemerintah Provinsi Riau.
                @else
                    Monitoring operasional pembangunan aplikasi daerah, pengawasan mutu teknis APTIKA, evaluasi CSIRT, serta disposisi kendala proyek.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="{{ route('eksekutif.project.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition flex items-center gap-1.5 backdrop-blur-xs">
                <span class="material-symbols-outlined text-[18px]">account_tree</span>
                <span>Portofolio Proyek</span>
            </a>
            <a href="{{ route('eksekutif.laporan.index') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-blue-50 text-[#0D47A1] text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Ringkasan Eksekutif</span>
            </a>
        </div>
    </div>

    <!-- Alert Permohonan Penundaan Menunggu Disposisi -->
    @if ($penundaanPendingCount > 0)
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs animate-bounce">
                    <span class="material-symbols-outlined text-[22px]">notification_important</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-rose-950">
                        Perhatian: Terdapat {{ $penundaanPendingCount }} Permohonan Penundaan Proyek Menunggu Disposisi
                    </h3>
                    <p class="text-xs text-rose-800 mt-0.5">
                        Tim Pengembang mengajukan permohonan penyesuaian target akibat kendala teknis/infrastruktur. Diperlukan keputusan persetujuan dari Pimpinan Bidang.
                    </p>
                </div>
            </div>
            <a href="{{ route('eksekutif.penundaan.index') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shrink-0 flex items-center gap-1.5 shadow-xs">
                <span>Tinjau Disposisi</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    @endif

    <!-- 4 Metrik Kartu Eksekutif -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Kartu 1: Total Aplikasi Daerah -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aplikasi Daerah</span>
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">apps</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $totalAplikasi }}</div>
                <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $aplikasiAktif }} Aktif
                    </span>
                    <span>&bull;</span>
                    <span class="text-amber-600 font-semibold">{{ $aplikasiPengembangan }} Dev</span>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Proyek Pengembangan Aktif -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Proyek Berjalan</span>
                <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">engineering</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $projectsBerjalan }}</div>
                <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-500">
                    <span class="font-semibold text-indigo-700">Rerata Capaian: {{ $avgProgres }}%</span>
                </div>
            </div>
        </div>

        <!-- Kartu 3: Proyek Live / Operasi -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Telah Go-Live</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">verified</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $projectsLive }}</div>
                <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-500">
                    <span class="text-emerald-700 font-medium">Operasional Penuh di OPD</span>
                </div>
            </div>
        </div>

        <!-- Kartu 4: Kendala & Penundaan -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tertunda / Kendala</span>
                <span class="w-9 h-9 rounded-xl {{ $projectsTertunda > 0 ? 'bg-rose-50 text-rose-700' : 'bg-slate-50 text-slate-600' }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">pause_circle</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black {{ $projectsTertunda > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $projectsTertunda }}</div>
                <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-500">
                    <span>{{ $penundaanPendingCount }} Permohonan Baru</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Dua Kolom: Tabel Portofolio & Widget Pimpinan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Kolom Kiri: Portofolio Proyek Prioritas (2/3) -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Tabel Monitoring Proyek Berjalan -->
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600">developer_mode</span>
                            <span>Monitoring Proyek Pembangunan Terkini</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pantauan progres tahapan teknis sprint, pengujian, dan timeline kerja</p>
                    </div>
                    <a href="{{ route('eksekutif.project.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">
                        <span>Lihat Semua</span>
                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-6">Nama Proyek & OPD</th>
                                <th class="py-3 px-6">Capaian Progres</th>
                                <th class="py-3 px-6 text-center">Status Operasional</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($proyekTerbaru as $proj)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-4 px-6">
                                        <a href="{{ route('eksekutif.project.show', $proj) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block">
                                            {{ $proj->nama_project }}
                                        </a>
                                        <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                                            <span>{{ $proj->aplikasi->instansi->nama_instansi ?? 'Pemerintah Provinsi Riau' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 min-w-[160px]">
                                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                                            <span class="text-slate-700">{{ $proj->progres_total }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-300 {{ $proj->progres_total >= 100 ? 'bg-emerald-500' : ($proj->progres_total > 50 ? 'bg-indigo-600' : 'bg-amber-500') }}" style="width: {{ $proj->progres_total }}%"></div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                                            @if ($proj->status === 'live') bg-emerald-50 text-emerald-700 border border-emerald-200
                                            @elseif ($proj->status === 'delayed') bg-rose-50 text-rose-700 border border-rose-200
                                            @elseif ($proj->status === 'deployment') bg-cyan-50 text-cyan-700 border border-cyan-200
                                            @elseif ($proj->status === 'security_testing') bg-purple-50 text-purple-700 border border-purple-200
                                            @elseif ($proj->status === 'functional_testing') bg-teal-50 text-teal-700 border border-teal-200
                                            @else bg-blue-50 text-blue-700 border border-blue-200
                                            @endif">
                                            {{ str_replace('_', ' ', $proj->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <a href="{{ route('eksekutif.project.show', $proj) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                            <span>Detail</span>
                                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-xs text-slate-400">
                                        Belum ada proyek pembangunan aplikasi yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sebaran Aplikasi per OPD (Top Instansi) -->
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-amber-600">domain</span>
                            <span>Sebaran Aplikasi Berdasarkan Perangkat Daerah (OPD)</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Instansi dengan volume integrasi dan kepemilikan aplikasi daerah tertinggi</p>
                    </div>
                    <a href="{{ route('eksekutif.aplikasi.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                        Lihat Seluruh OPD
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
                    @forelse ($sebaranOpd as $opd)
                        <div class="p-4 rounded-xl border border-slate-200/70 bg-slate-50/50 flex items-center justify-between">
                            <div class="min-w-0 pr-3">
                                <h4 class="text-xs font-bold text-slate-900 truncate" title="{{ $opd->nama_instansi }}">
                                    {{ $opd->nama_instansi }}
                                </h4>
                                <span class="text-[11px] text-slate-400 block mt-0.5">{{ $opd->singkatan ?? 'Provinsi Riau' }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-black text-slate-800 shadow-2xs shrink-0">
                                {{ $opd->aplikasi_count }} Aplikasi
                            </span>
                        </div>
                    @empty
                        <div class="col-span-2 text-center text-xs text-slate-400 py-6">
                            Belum ada data instansi yang tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Disposisi & Agenda Rapat (1/3) -->
        <div class="space-y-6">

            <!-- Permohonan Penundaan Menunggu Keputusan -->
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-rose-600">pending_actions</span>
                        <span>Antrean Penundaan</span>
                    </h3>
                    @if ($penundaanPendingCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">
                            {{ $penundaanPendingCount }} Menunggu
                        </span>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 mt-2">
                    @forelse ($penundaanTerbaru as $p)
                        <div class="py-3.5 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 truncate max-w-[180px]">
                                    {{ $p->project->nama_project ?? '-' }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $p->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                "{{ $p->alasan }}"
                            </p>
                            <div class="flex items-center justify-between pt-1 text-xs">
                                <span class="text-[11px] text-slate-400">Pengaju: {{ $p->pengaju->name ?? 'Lead' }}</span>
                                <a href="{{ route('eksekutif.penundaan.index') }}" class="font-bold text-rose-600 hover:underline">
                                    Disposisi &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            <span class="material-symbols-outlined text-[28px] text-emerald-500 mb-1 block">check_circle</span>
                            Tidak ada permohonan penundaan yang tertunda. Seluruh proyek berjalan normal.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Agenda Rapat Koordinasi Mendatang -->
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-indigo-600">event_upcoming</span>
                        <span>Agenda Rapat Koordinasi</span>
                    </h3>
                </div>

                <div class="space-y-3 mt-4">
                    @forelse ($rapatMendatang as $rapat)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-900">{{ $rapat->nama_rapat }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-indigo-600">schedule</span>
                                <span>{{ $rapat->waktu_rapat->format('d M Y - H:i') }} WIB</span>
                            </div>
                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">location_on</span>
                                <span>{{ $rapat->tempat_rapat ?? 'Ruang Rapat APTIKA' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Belum ada jadwal rapat koordinasi terdekat.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
