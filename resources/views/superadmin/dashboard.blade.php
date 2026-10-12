@extends('layouts.admin')

@section('title', 'Dashboard Superadmin')
@section('page-title', 'Dashboard Ringkasan')
@section('breadcrumb', 'Panel Kontrol Utama Superadmin')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0D47A1] via-[#165eae] to-[#1e88e5] p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-xs text-xs font-semibold text-white mb-3">
                <span class="material-symbols-outlined text-[16px]">verified</span>
                <span>Superadmin Access</span>
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-2 text-sm text-blue-100/90 leading-relaxed">
                Anda memiliki akses penuh untuk mengelola master data instansi OPD, manajemen pengguna seluruh peran, dan direktori aplikasi pada sistem SIMBANGDA Diskominfo Provinsi Riau.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 pointer-events-none hidden md:block">
            <span class="material-symbols-outlined text-[180px]">admin_panel_settings</span>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Instansi Card -->
        <a href="{{ route('superadmin.instansi.index') }}" class="group block p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#0D47A1]/50 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">domain</span>
                </div>
                <span class="material-symbols-outlined text-[20px] text-slate-300 group-hover:text-[#0D47A1] transition">arrow_forward</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['total_instansi'] }}</div>
            <p class="text-xs font-semibold text-slate-500 mt-1">Instansi OPD Terdaftar</p>
        </a>

        <!-- Users Card -->
        <a href="{{ route('superadmin.user.index') }}" class="group block p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#0D47A1]/50 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">group</span>
                </div>
                <span class="material-symbols-outlined text-[20px] text-slate-300 group-hover:text-indigo-700 transition">arrow_forward</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['total_users'] }}</div>
            <p class="text-xs font-semibold text-slate-500 mt-1">Total Pengguna Sistem</p>
        </a>

        <!-- Anggota Tim Card -->
        <a href="{{ route('superadmin.anggota-tim.index') }}" class="group block p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#0D47A1]/50 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">badge</span>
                </div>
                <span class="material-symbols-outlined text-[20px] text-slate-300 group-hover:text-teal-700 transition">arrow_forward</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['total_anggota_tim'] }}</div>
            <p class="text-xs font-semibold text-slate-500 mt-1">Personil Tim Pengembang</p>
        </a>

        <!-- Aplikasi Card -->
        <a href="{{ route('superadmin.aplikasi.index') }}" class="group block p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#0D47A1]/50 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">apps</span>
                </div>
                <span class="material-symbols-outlined text-[20px] text-slate-300 group-hover:text-emerald-700 transition">arrow_forward</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['total_aplikasi'] }}</div>
            <p class="text-xs font-semibold text-slate-500 mt-1">Aplikasi Terdata</p>
        </a>

        <!-- Pengajuan Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">description</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-semibold">Total</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['total_pengajuan'] }}</div>
            <p class="text-xs font-semibold text-slate-500 mt-1">Total Permohonan Masuk</p>
        </div>
    </div>

    <!-- Quick Action Shortcuts -->
    <div class="rounded-2xl bg-white border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#0D47A1] text-[20px]">bolt</span>
            <span>Aksi Cepat Master Data</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('superadmin.instansi.create') }}" class="flex items-center gap-3 p-4 rounded-xl border border-dashed border-slate-200 hover:border-[#0D47A1] hover:bg-blue-50/40 text-slate-700 hover:text-[#0D47A1] transition group">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-[#0D47A1] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">add_business</span>
                </div>
                <div>
                    <span class="text-xs font-bold block">Tambah Instansi OPD</span>
                    <span class="text-[11px] text-slate-400 group-hover:text-slate-500">Daftarkan dinas baru</span>
                </div>
            </a>

            <a href="{{ route('superadmin.anggota-tim.create') }}" class="flex items-center gap-3 p-4 rounded-xl border border-dashed border-slate-200 hover:border-[#0D47A1] hover:bg-blue-50/40 text-slate-700 hover:text-[#0D47A1] transition group">
                <div class="w-10 h-10 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                </div>
                <div>
                    <span class="text-xs font-bold block">Tambah Anggota Tim</span>
                    <span class="text-[11px] text-slate-400 group-hover:text-slate-500">Personil pengembang internal</span>
                </div>
            </a>

            <a href="{{ route('superadmin.user.create') }}" class="flex items-center gap-3 p-4 rounded-xl border border-dashed border-slate-200 hover:border-[#0D47A1] hover:bg-blue-50/40 text-slate-700 hover:text-[#0D47A1] transition group">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                </div>
                <div>
                    <span class="text-xs font-bold block">Tambah Pengguna Baru</span>
                    <span class="text-[11px] text-slate-400 group-hover:text-slate-500">Akun sistem seluruh peran</span>
                </div>
            </a>

            <a href="{{ route('superadmin.aplikasi.create') }}" class="flex items-center gap-3 p-4 rounded-xl border border-dashed border-slate-200 hover:border-[#0D47A1] hover:bg-blue-50/40 text-slate-700 hover:text-[#0D47A1] transition group">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">post_add</span>
                </div>
                <div>
                    <span class="text-xs font-bold block">Daftarkan Aplikasi</span>
                    <span class="text-[11px] text-slate-400 group-hover:text-slate-500">Entri direktori aplikasi OPD</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Tables Grid: Recent Users & Recent Aplikasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users Table -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0D47A1] text-[20px]">group</span>
                    <span>Pengguna Terbaru</span>
                </h3>
                <a href="{{ route('superadmin.user.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentUsers as $user)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $user->email }} ({{ $user->username ?? '-' }})</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-[#0D47A1] border border-blue-100 uppercase">
                                {{ $user->roles->first()->name ?? 'Tanpa Role' }}
                            </span>
                            @if ($user->instansi)
                                <p class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[120px]">{{ $user->instansi->nama_instansi }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada data pengguna</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Aplikasi Table -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">apps</span>
                    <span>Aplikasi Terdaftar Terbaru</span>
                </h3>
                <a href="{{ route('superadmin.aplikasi.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentAplikasi as $app)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $app->nama_aplikasi }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ $app->instansi->nama_instansi ?? 'Tidak Terhubung' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            @if ($app->status === 'aktif')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @elseif ($app->status === 'pengembangan')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Pengembangan</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada aplikasi yang terdaftar</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
