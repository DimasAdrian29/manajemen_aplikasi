@extends('layouts.opd')

@section('title', 'Dashboard OPD')
@section('page-title', 'Dashboard Layanan OPD')
@section('breadcrumb', 'Pusat Layanan Fasilitasi & Pengajuan Aplikasi')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0D47A1] via-[#1565C0] to-[#1976D2] p-6 sm:p-8 text-white shadow-lg shadow-[#0D47A1]/15">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-xs mb-3 border border-white/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Portal Resmi Diskominfo Riau</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                Selamat Datang, {{ $user->name }}
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                Kelola usulan pembangunan aplikasi baru, permintaan pengembangan fitur, maupun penonaktifan sistem perangkat daerah Anda secara terintegrasi dan akuntabel.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                @can('pengajuan.create')
                    <a href="{{ route('opd.pengajuan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#0D47A1] text-xs font-bold hover:bg-blue-50 shadow-sm transition">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Ajukan Permohonan Baru</span>
                    </a>
                @endcan
                @can('pengajuan.view')
                    <a href="{{ route('opd.pengajuan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 backdrop-blur-xs transition">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                        <span>Lihat Riwayat Status</span>
                    </a>
                @endcan
            </div>
        </div>
        <!-- Decorative Background Icon -->
        <div class="absolute -right-6 -bottom-6 text-white/10 pointer-events-none select-none">
            <span class="material-symbols-outlined text-[200px]">domain</span>
        </div>
    </div>

    <!-- Metric Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @can('pengajuan.view')
        <!-- Total -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Pengajuan</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">folder</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['total_pengajuan'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Semua permohonan</div>
        </div>

        <!-- Menunggu Telaah -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Menunggu Telaah</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-600 mt-2">{{ $stats['menunggu_telaah'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Dalam proses review</div>
        </div>

        <!-- Disetujui -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Disetujui</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600 mt-2">{{ $stats['disetujui'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Siap masuk proyek</div>
        </div>

        <!-- Ditolak -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Ditolak / Revisi</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-rose-600 mt-2">{{ $stats['ditolak'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Belum memenuhi syarat</div>
        </div>
        @endcan

        @can('master.aplikasi.view')
        <!-- Katalog Aplikasi -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Aplikasi Terdaftar</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">apps</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['total_aplikasi'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Milik instansi Anda</div>
        </div>
        @endcan
    </div>

    @can('pengajuan.create')
    <!-- Quick Service Selection Cards -->
    <div>
        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">category</span>
            <span>Jenis Layanan Fasilitasi Aplikasi Daerah</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @forelse ($layanans as $layanan)
                <a href="{{ route('opd.pengajuan.create', ['layanan' => $layanan->slug]) }}" class="group block p-6 rounded-2xl bg-white border border-slate-200/80 hover:border-[#0D47A1]/40 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition" style="background-color: {{ $layanan->warna }}18; color: {{ $layanan->warna }};">
                        <span class="material-symbols-outlined text-[26px]">{{ $layanan->ikon ?? 'category' }}</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0D47A1] transition">{{ $layanan->nama }}</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed line-clamp-2">
                        {{ $layanan->deskripsi }}
                    </p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-bold text-[#0D47A1]">
                        <span>Mulai Permohonan</span>
                        <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition">arrow_forward</span>
                    </div>
                </a>
            @empty
                <div class="col-span-full p-8 text-center bg-white rounded-2xl border border-slate-200">
                    <p class="text-xs text-slate-400">Belum ada layanan aktif yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
    @endcan

    @can('pengajuan.view')
    <!-- Recent Submissions Table -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Permohonan Terbaru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pelacakan proses review & telaah oleh Dinas Kominfo</p>
            </div>
            <a href="{{ route('opd.pengajuan.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                <span>Lihat Semua</span>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">No. Registrasi</th>
                        <th class="py-3.5 px-6">Layanan</th>
                        <th class="py-3.5 px-6">Nama Aplikasi / Usulan</th>
                        <th class="py-3.5 px-6">Tanggal Kirim</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($recentPengajuan as $p)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-[#0D47A1]">
                                <a href="{{ route('opd.pengajuan.show', $p) }}" class="hover:underline">
                                    {{ $p->kode_pengajuan }}
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $p->nama_layanan }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-900">
                                {{ $p->nama_aplikasi ?? ($p->aplikasi->nama_aplikasi ?? '-') }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $p->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($p->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Terkirim</span>
                                    </span>
                                @elseif ($p->status === 'under_review')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        <span>Dalam Telaah</span>
                                    </span>
                                @elseif ($p->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Disetujui</span>
                                    </span>
                                @elseif ($p->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Ditolak</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        {{ $p->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('opd.pengajuan.show', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#0D47A1] bg-blue-50 hover:bg-blue-100 transition">
                                    <span>Lacak</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">assignment_turned_in</span>
                                <p class="text-sm font-semibold">Belum ada riwayat permohonan yang diajukan.</p>
                                <a href="{{ route('opd.pengajuan.create') }}" class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-[#0D47A1] hover:underline">
                                    <span>Ajukan permohonan sekarang</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endcan
</div>
@endsection
