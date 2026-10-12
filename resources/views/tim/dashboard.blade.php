@extends('layouts.tim')

@section('title', 'Dashboard Tim Pengembang')
@section('page-title', 'Dashboard Tim Pengembang')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-8">

    <!-- Welcome Card -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0D47A1] via-[#1565C0] to-slate-900 p-6 sm:p-8 text-white relative overflow-hidden shadow-sm">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-blue-100 border border-white/15 mb-3 backdrop-blur-xs">
                <span class="material-symbols-outlined text-[16px] text-amber-400">terminal</span>
                <span>Workspace Tenaga Pengembang</span>
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-xs sm:text-sm text-blue-100 mt-2 leading-relaxed">
                Portal kerja terpadu untuk memantau sprint, dokumen perancangan, dan jadwal koordinasi pengembangan aplikasi perangkat daerah.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <span class="material-symbols-outlined text-[200px]">code</span>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total Proyek -->
        <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penugasan</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">folder_code</span>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-3">{{ $totalProjectsCount }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Proyek aplikasi terdaftar</div>
        </div>

        <!-- Sebagai Ketua Tim -->
        <div class="rounded-2xl bg-white border border-amber-200/80 p-5 shadow-xs bg-gradient-to-b from-amber-50/30 to-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Ketua Tim (Lead)</span>
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">workspace_premium</span>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-950 mt-3">{{ $leadProjectsCount }}</div>
            <div class="text-[11px] text-amber-700 font-medium mt-1">Kelola progres & tahapan</div>
        </div>

        <!-- Sebagai Anggota Tim -->
        <div class="rounded-2xl bg-white border border-blue-200/80 p-5 shadow-xs bg-gradient-to-b from-blue-50/30 to-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-800 uppercase tracking-wider">Anggota Tim</span>
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-blue-950 mt-3">{{ $memberProjectsCount }}</div>
            <div class="text-[11px] text-blue-700 font-medium mt-1">Personil pelaksana teknis</div>
        </div>

        <!-- Proyek Aktif -->
        <div class="rounded-2xl bg-white border border-emerald-200/80 p-5 shadow-xs bg-gradient-to-b from-emerald-50/30 to-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Sedang Berjalan</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-950 mt-3">{{ $activeProjectsCount }}</div>
            <div class="text-[11px] text-emerald-700 font-medium mt-1">Sprint aktif dikerjakan</div>
        </div>
    </div>

    <!-- Section 1: Proyek yang Saya Pimpin (Sebagai Ketua Tim) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[22px] text-amber-600">workspace_premium</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Proyek yang Anda Pimpin (Sebagai Ketua Tim)</h3>
                    <p class="text-xs text-slate-500">Anda bertanggung jawab penuh atas pembaruan progres sprint, dokumen arsitektur, dan koordinasi teknis.</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                {{ $leadProjects->count() }} Proyek
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($leadProjects as $project)
                <div class="rounded-2xl bg-white border border-amber-200/90 shadow-xs hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-white uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                <span class="material-symbols-outlined text-[13px]">stars</span>
                                <span>Ketua Tim</span>
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                @if ($project->status === 'live') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif ($project->status === 'delayed') bg-rose-50 text-rose-700 border border-rose-200
                                @elseif ($project->status === 'deployment') bg-cyan-50 text-cyan-700 border border-cyan-200
                                @elseif ($project->status === 'security_testing') bg-purple-50 text-purple-700 border border-purple-200
                                @elseif ($project->status === 'functional_testing') bg-teal-50 text-teal-700 border border-teal-200
                                @else bg-blue-50 text-blue-700 border border-blue-200
                                @endif">
                                {{ str_replace('_', ' ', $project->status) }}
                            </span>
                        </div>

                        <h4 class="text-sm font-bold text-slate-900 line-clamp-2">{{ $project->nama_project }}</h4>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-slate-400">account_balance</span>
                            <span class="truncate">{{ $project->aplikasi->instansi->nama_instansi ?? 'OPD Pemohon' }}</span>
                        </p>

                        <!-- Progress Bar -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="text-slate-500 font-medium">Progres Sprint</span>
                                <span class="font-bold text-slate-800">{{ $project->progres_total }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#0D47A1] h-2 rounded-full transition-all duration-300" style="width: {{ $project->progres_total }}%"></div>
                            </div>
                        </div>

                        <!-- Info Anggota & Timeline -->
                        <div class="mt-4 flex items-center justify-between text-[11px] text-slate-400">
                            <span class="flex items-center gap-1 text-slate-500">
                                <span class="material-symbols-outlined text-[15px]">group</span>
                                <span>{{ $project->tim->count() }} Personil</span>
                            </span>
                            <span class="flex items-center gap-1 text-slate-500">
                                <span class="material-symbols-outlined text-[15px]">event</span>
                                <span>{{ $project->tanggal_selesai ? $project->tanggal_selesai->format('d M Y') : 'Selesai' }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50/70 border-t border-slate-100">
                        <a href="{{ route('tim.project.show', $project) }}" class="w-full py-2 px-3 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">edit_note</span>
                            <span>Kelola Progres & Dokumen</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center bg-white rounded-2xl border border-dashed border-slate-200 text-slate-400">
                    <span class="material-symbols-outlined text-[32px] text-slate-300 block mb-1">sentiment_satisfied</span>
                    <p class="text-xs font-medium">Saat ini Anda belum ditunjuk sebagai Ketua Tim pada proyek berjalan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Proyek yang Saya Ikuti (Sebagai Anggota Tim) -->
    <div class="space-y-4 pt-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[22px] text-blue-600">group</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Proyek yang Anda Ikuti (Sebagai Anggota Tim)</h3>
                    <p class="text-xs text-slate-500">Pelaporan progres dikoordinasikan secara terpusat oleh Ketua Tim proyek.</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                {{ $memberProjects->count() }} Proyek
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($memberProjects as $project)
                @php
                    $leadUser = $project->tim->where('peran', 'ketua_tim')->first()?->user;
                @endphp
                <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">person</span>
                                <span>Anggota Tim</span>
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                @if ($project->status === 'live') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif ($project->status === 'delayed') bg-rose-50 text-rose-700 border border-rose-200
                                @else bg-slate-100 text-slate-600
                                @endif">
                                {{ str_replace('_', ' ', $project->status) }}
                            </span>
                        </div>

                        <h4 class="text-sm font-bold text-slate-900 line-clamp-2">{{ $project->nama_project }}</h4>
                        
                        <!-- Info Ketua Tim -->
                        <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ substr($leadUser->name ?? 'K', 0, 2) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-[10px] text-slate-400 leading-tight">Ketua Tim (Lead):</div>
                                <div class="text-xs font-bold text-slate-800 truncate">{{ $leadUser->name ?? 'Belum ditentukan' }}</div>
                            </div>
                        </div>

                        <!-- Progress Bar (Monitoring Only) -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="text-slate-500 font-medium">Progres Saat Ini</span>
                                <span class="font-bold text-slate-800">{{ $project->progres_total }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progres_total }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50/70 border-t border-slate-100">
                        <a href="{{ route('tim.project.show', $project) }}" class="w-full py-2 px-3 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Lihat Informasi Proyek</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center bg-white rounded-2xl border border-dashed border-slate-200 text-slate-400">
                    <span class="material-symbols-outlined text-[32px] text-slate-300 block mb-1">assignment_turned_in</span>
                    <p class="text-xs font-medium">Belum ada penugasan sebagai anggota tim pada proyek lain.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 3: Agenda Rapat Mendatang -->
    @if ($upcomingRapats->isNotEmpty())
        <div class="rounded-2xl bg-white border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">groups</span>
                    <h3 class="text-sm font-bold text-slate-900">Agenda Rapat Koordinasi Mendatang</h3>
                </div>
                <a href="{{ route('tim.rapat.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:text-[#165eae] flex items-center gap-1 transition">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($upcomingRapats as $rapat)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-50 transition flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-slate-900 line-clamp-1">{{ $rapat->nama_rapat }}</div>
                            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px] text-[#0D47A1]">calendar_today</span>
                                    <span>{{ $rapat->waktu_rapat->format('d M Y, H:i') }} WIB</span>
                                </span>
                                <span>&bull;</span>
                                <span class="truncate max-w-[150px]">{{ $rapat->tempat_rapat ?? 'Online' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('tim.rapat.show', $rapat) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-[#0D47A1] hover:bg-blue-100 text-xs font-bold shrink-0 transition">
                            Presensi
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
