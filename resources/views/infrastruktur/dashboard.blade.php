@extends('layouts.infrastruktur')

@section('title', 'Dashboard Infrastruktur')
@section('page_title', 'Dashboard Infrastruktur & Jaringan')
@section('page_subtitle', 'Monitoring operasional deployment, kapasitas server, sertifikat SSL, dan terminasi sistem')

@section('content')
<div class="space-y-6">

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Antrean Deployment -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Antrean Deployment</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $pendingDeploymentCount }}</span>
                    <span class="text-xs font-medium text-amber-600">Menunggu Deploy</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                <span class="material-symbols-outlined text-[26px]">rocket_launch</span>
            </div>
        </div>

        <!-- Aplikasi Live -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aplikasi Live</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $liveAppsCount }}</span>
                    <span class="text-xs font-medium text-emerald-600">Online & Aktif</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <span class="material-symbols-outlined text-[26px]">dns</span>
            </div>
        </div>

        <!-- Peringatan SSL Expiring -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">SSL Expiring (&le;30h)</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold {{ $sslExpiringCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $sslExpiringCount }}</span>
                    <span class="text-xs font-medium {{ $sslExpiringCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-500' }}">Perlu Perpanjangan</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $sslExpiringCount > 0 ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-50 text-slate-600 border-slate-100' }} flex items-center justify-center shrink-0 border">
                <span class="material-symbols-outlined text-[26px]">lock_clock</span>
            </div>
        </div>

        <!-- Antrean Penonaktifan -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Antrean Penonaktifan</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $pendingDeactivationCount }}</span>
                    <span class="text-xs font-medium text-slate-500">Disetujui APTIKA</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                <span class="material-symbols-outlined text-[26px]">power_settings_new</span>
            </div>
        </div>
    </div>

    <!-- Server Resource Summary Banner -->
    <div class="p-5 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white rounded-2xl shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <h3 class="text-base font-bold">Total Sumber Daya Komputasi Server Riau</h3>
                </div>
                <p class="text-xs text-slate-300 mt-1">Akumulasi resource VPS, cloud, dan on-premise yang terdaftar pada sistem SIMBANGDA</p>
            </div>
            <div class="grid grid-cols-3 gap-6 bg-white/10 backdrop-blur-xs px-5 py-3 rounded-xl border border-white/10">
                <div class="text-center">
                    <span class="text-xs text-slate-300 block">Total CPU</span>
                    <span class="text-lg font-bold text-white">{{ $totalCpu }} Cores</span>
                </div>
                <div class="text-center border-x border-white/15 px-4">
                    <span class="text-xs text-slate-300 block">Total RAM</span>
                    <span class="text-lg font-bold text-cyan-300">{{ $totalRam }} GB</span>
                </div>
                <div class="text-center">
                    <span class="text-xs text-slate-300 block">Total Disk</span>
                    <span class="text-lg font-bold text-emerald-300">{{ $totalStorage }} GB</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Deployment Queue & Recent Deployments -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Antrean Deployment Siap Rilis -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0D47A1]">rocket_launch</span>
                        <h2 class="text-sm font-bold text-slate-900">Antrean Siap Deployment</h2>
                    </div>
                    <a href="{{ route('infrastruktur.deployment.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                        Lihat Semua ({{ $pendingDeploymentCount }})
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>

                @if ($pendingProjects->isEmpty())
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-[24px]">task_alt</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Semua Proyek Telah Dideploy</p>
                        <p class="text-xs text-slate-400 mt-1">Saat ini tidak ada antrean proyek dengan status deployment.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($pendingProjects as $project)
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $project->nama_project }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Siap Deploy
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[14px] text-slate-400">domain</span>
                                        {{ $project->aplikasi?->instansi?->nama_instansi ?? 'Instansi' }}
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('infrastruktur.deployment.create', $project) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] shadow-xs transition">
                                        <span class="material-symbols-outlined text-[16px]">settings_ethernet</span>
                                        Input Hosting & Live
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Riwayat Deployments Terakhir -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                        <h2 class="text-sm font-bold text-slate-900">Aplikasi Baru Selesai Dideploy</h2>
                    </div>
                    <a href="{{ route('infrastruktur.hosting.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                        Inventaris Lengkap
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>

                @if ($recentDeployments->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada aplikasi yang dideploy melalui modul ini.
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentDeployments as $item)
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900">{{ $item->nama_domain ?? '-' }}</span>
                                        @if ($item->ssl_aktif)
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                <span class="material-symbols-outlined text-[12px]">lock</span> SSL
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $item->project?->aplikasi?->nama_aplikasi ?? $item->project?->nama_project ?? '-' }} &bull;
                                        <span class="font-mono text-slate-600">{{ $item->ip_server ?? 'IP: -' }}</span> &bull;
                                        <span>{{ strtoupper($item->jenis_hosting ?? 'vps') }}</span>
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    @if ($item->url_aplikasi)
                                        <a href="{{ $item->url_aplikasi }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-[#0D47A1] hover:bg-blue-50 transition" title="Kunjungi Situs">
                                            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('infrastruktur.hosting.show', $item) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Col: SSL Warnings & Quick Links -->
        <div class="space-y-6">

            <!-- SSL Expiring Alert Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-600">lock_clock</span>
                        <h3 class="text-sm font-bold text-slate-900">Pemantauan SSL (Expiring)</h3>
                    </div>
                </div>

                @if ($sslExpiringList->isEmpty())
                    <div class="p-6 text-center">
                        <span class="material-symbols-outlined text-emerald-500 text-[32px] block mb-1">verified_user</span>
                        <p class="text-xs font-semibold text-slate-700">Semua Sertifikat SSL Aman</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada sertifikat SSL yang akan kadaluarsa dalam 30 hari.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($sslExpiringList as $sslItem)
                            @php
                                $daysLeft = now()->diffInDays($sslItem->ssl_kadaluarsa, false);
                            @endphp
                            <div class="p-4 hover:bg-slate-50 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $sslItem->nama_domain }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $sslItem->project?->aplikasi?->nama_aplikasi ?? '-' }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $daysLeft <= 7 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $daysLeft <= 0 ? 'Hari ini' : $daysLeft . ' hari lagi' }}
                                    </span>
                                </div>
                                <div class="mt-2 text-[11px] text-slate-400 flex items-center justify-between">
                                    <span>Exp: {{ $sslItem->ssl_kadaluarsa?->format('d M Y') }}</span>
                                    <a href="{{ route('infrastruktur.hosting.edit', $sslItem) }}" class="text-[#0D47A1] font-semibold hover:underline">Perbarui</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Quick Action Cards -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-cyan-600">bolt</span>
                    Akses Cepat
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('infrastruktur.deployment.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0D47A1] flex items-center justify-center group-hover:scale-105 transition">
                                <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Antrean Deployment</span>
                                <span class="text-[11px] text-slate-500 block">Deploy proyek siap rilis</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 group-hover:text-[#0D47A1] text-[18px]">chevron_right</span>
                    </a>

                    <a href="{{ route('infrastruktur.hosting.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center group-hover:scale-105 transition">
                                <span class="material-symbols-outlined text-[18px]">dns</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Inventaris Server</span>
                                <span class="text-[11px] text-slate-500 block">Spesifikasi & domain aktif</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 group-hover:text-[#0D47A1] text-[18px]">chevron_right</span>
                    </a>

                    <a href="{{ route('infrastruktur.penonaktifan.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center group-hover:scale-105 transition">
                                <span class="material-symbols-outlined text-[18px]">power_settings_new</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Antrean Penonaktifan</span>
                                <span class="text-[11px] text-slate-500 block">Terminasi server & domain</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 group-hover:text-purple-700 text-[18px]">chevron_right</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
