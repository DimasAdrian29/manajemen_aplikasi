@extends('layouts.tim')

@section('title', 'Proyek Saya')
@section('page-title', 'Daftar Proyek Saya')
@section('breadcrumb', 'Proyek Saya')

@section('content')
<div class="space-y-6">

    <!-- Filter and Search Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Tab Filter -->
        <div class="flex items-center gap-1.5 p-1 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-semibold">
            <a href="{{ route('tim.project.index', ['tab' => 'semua', 'search' => $search]) }}" class="px-3.5 py-1.5 rounded-lg transition {{ $tab === 'semua' ? 'bg-[#0D47A1] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                Semua Proyek
            </a>
            <a href="{{ route('tim.project.index', ['tab' => 'ketua', 'search' => $search]) }}" class="px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ $tab === 'ketua' ? 'bg-amber-500 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                <span class="material-symbols-outlined text-[14px]">stars</span>
                <span>Sebagai Ketua</span>
            </a>
            <a href="{{ route('tim.project.index', ['tab' => 'anggota', 'search' => $search]) }}" class="px-3.5 py-1.5 rounded-lg transition {{ $tab === 'anggota' ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                Sebagai Anggota
            </a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('tim.project.index') }}" class="flex items-center gap-2 max-w-sm w-full">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama proyek, aplikasi..." 
                    class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
                >
            </div>
            <button type="submit" class="px-3.5 py-2 bg-[#0D47A1] text-white text-xs font-bold rounded-xl hover:bg-[#165eae] transition shrink-0 cursor-pointer">
                Cari
            </button>
            @if ($search)
                <a href="{{ route('tim.project.index', ['tab' => $tab]) }}" class="px-2.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 text-xs rounded-xl shrink-0">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Project Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($projects as $project)
            @php
                $isLead = $project->tim->where('user_id', auth()->id())->where('peran', 'ketua_tim')->isNotEmpty();
                $leadUser = $project->tim->where('peran', 'ketua_tim')->first()?->user;
            @endphp
            <div class="rounded-2xl bg-white border {{ $isLead ? 'border-amber-200/90' : 'border-slate-200/80' }} shadow-xs hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                <div class="p-5">
                    <!-- Top Badges -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        @if ($isLead)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-white uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                <span class="material-symbols-outlined text-[13px]">stars</span>
                                <span>Ketua Tim</span>
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">person</span>
                                <span>Anggota Tim</span>
                            </span>
                        @endif

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

                    @if (! $isLead && $leadUser)
                        <div class="mt-3 p-2 rounded-lg bg-slate-50 text-[11px] text-slate-600 flex items-center gap-2">
                            <span class="text-slate-400">Ketua:</span>
                            <span class="font-bold text-slate-800 truncate">{{ $leadUser->name }}</span>
                        </div>
                    @endif

                    <!-- Progress Bar -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="text-slate-500 font-medium">Progres Sprint</span>
                            <span class="font-bold text-slate-800">{{ $project->progres_total }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="{{ $isLead ? 'bg-amber-500' : 'bg-blue-600' }} h-2 rounded-full transition-all duration-300" style="width: {{ $project->progres_total }}%"></div>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="mt-4 flex items-center justify-between text-[11px] text-slate-400">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">group</span>
                            <span>{{ $project->tim->count() }} Personil</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">event</span>
                            <span>{{ $project->tanggal_selesai ? $project->tanggal_selesai->format('d M Y') : '-' }}</span>
                        </span>
                    </div>
                </div>

                <div class="p-3 bg-slate-50/70 border-t border-slate-100">
                    <a href="{{ route('tim.project.show', $project) }}" class="w-full py-2 px-3 rounded-xl {{ $isLead ? 'bg-[#0D47A1] hover:bg-[#165eae] text-white font-bold' : 'bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold' }} text-xs transition flex items-center justify-center gap-1.5 shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">{{ $isLead ? 'edit_note' : 'visibility' }}</span>
                        <span>{{ $isLead ? 'Buka & Kelola Proyek' : 'Lihat Detail Proyek' }}</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-slate-200 text-slate-400">
                <span class="material-symbols-outlined text-[40px] text-slate-300 block mb-2">folder_off</span>
                <p class="text-sm font-semibold text-slate-700">Tidak ada proyek yang ditemukan.</p>
                <p class="text-xs text-slate-400 mt-1">Anda belum ditugaskan pada proyek dengan filter yang dipilih.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($projects->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            {{ $projects->links() }}
        </div>
    @endif

</div>
@endsection
