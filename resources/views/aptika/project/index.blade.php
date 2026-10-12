@extends('layouts.aptika')

@section('title', 'Manajemen Proyek')
@section('page-title', 'Manajemen Proyek Aplikasi Daerah')
@section('breadcrumb', 'Manajemen Layanan / Proyek Pengembangan')

@section('content')
<div class="space-y-6">
    <!-- Action Bar & Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('aptika.project.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-xl w-full">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama proyek, sistem, instansi..." 
                    class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
                >
            </div>

            <select 
                name="status" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
            >
                <option value="">Semua Tahapan Proyek</option>
                <option value="initiated" {{ $status === 'initiated' ? 'selected' : '' }}>Inisiasi Tim</option>
                <option value="in_development" {{ $status === 'in_development' ? 'selected' : '' }}>Pengembangan (Coding)</option>
                <option value="functional_testing" {{ $status === 'functional_testing' ? 'selected' : '' }}>Uji Fungsional (APTIKA)</option>
                <option value="security_testing" {{ $status === 'security_testing' ? 'selected' : '' }}>Uji Keamanan (Persandian)</option>
                <option value="deployment" {{ $status === 'deployment' ? 'selected' : '' }}>Hosting & Deployment</option>
                <option value="live" {{ $status === 'live' ? 'selected' : '' }}>Live / Beroperasi</option>
            </select>

            @if ($search || $status)
                <a href="{{ route('aptika.project.index') }}" class="px-2.5 py-2 text-xs text-slate-500 hover:text-slate-700 font-medium">
                    Reset
                </a>
            @endif
        </form>

        <!-- Inisiasi Proyek Button -->
        <a href="{{ route('aptika.project.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-sm shadow-[#0D47A1]/20 transition shrink-0">
            <span class="material-symbols-outlined text-[18px]">assignment_add</span>
            <span>Inisiasi Proyek Baru</span>
        </a>
    </div>

    <!-- Project Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($projects as $project)
            <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            @if ($project->status === 'initiated') bg-slate-100 text-slate-700
                            @elseif ($project->status === 'in_development') bg-purple-50 text-purple-700 border border-purple-200
                            @elseif ($project->status === 'functional_testing') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif ($project->status === 'security_testing') bg-amber-50 text-amber-700 border border-amber-200
                            @elseif ($project->status === 'deployment') bg-blue-50 text-blue-700 border border-blue-200
                            @elseif ($project->status === 'live') bg-teal-50 text-teal-700 border border-teal-200
                            @else bg-rose-50 text-rose-700 border border-rose-200
                            @endif">
                            {{ str_replace('_', ' ', $project->status) }}
                        </span>
                        <span class="font-mono text-xs text-slate-400">
                            #PRJ-{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 line-clamp-1" title="{{ $project->nama_project }}">
                        <a href="{{ route('aptika.project.show', $project) }}" class="hover:text-[#0D47A1] transition">
                            {{ $project->nama_project }}
                        </a>
                    </h4>

                    <div class="text-xs text-slate-500 line-clamp-1">
                        Instansi: <strong class="text-slate-700">{{ $project->aplikasi->instansi->nama_instansi ?? '-' }}</strong>
                    </div>

                    <!-- Progress Bar -->
                    <div>
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="text-slate-400">Progres Penyelesaian</span>
                            <span class="font-bold text-slate-700">{{ $project->progres_total }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#0D47A1] h-2 rounded-full transition-all duration-300" style="width: {{ $project->progres_total }}%"></div>
                        </div>
                    </div>

                    <!-- Tim Pengembang Avatar Stack -->
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <span class="material-symbols-outlined text-[16px] text-purple-600">badge</span>
                            <span class="font-medium truncate max-w-[140px]" title="{{ $project->tim->firstWhere('peran', 'ketua_tim')->user->name ?? 'Belum ada ketua' }}">
                                {{ $project->tim->firstWhere('peran', 'ketua_tim')->user->name ?? 'Belum ada ketua' }}
                            </span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[11px]">
                            +{{ max(0, $project->tim->count() - 1) }} staf
                        </span>
                    </div>

                    <!-- Timeline -->
                    <div class="text-[11px] text-slate-400 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                        <span>{{ $project->tanggal_mulai?->format('d M Y') }} s/d {{ $project->tanggal_selesai?->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('aptika.project.show', $project) }}" class="flex-1 py-1.5 px-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#0D47A1] text-xs font-bold text-center transition">
                        Detail Proyek
                    </a>
                    @if (in_array($project->status, ['in_development', 'functional_testing']))
                        <a href="{{ route('aptika.testing.create', ['project_id' => $project->id]) }}" class="py-1.5 px-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition" title="Uji Fungsional">
                            Uji
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl bg-white border border-slate-200/80 p-12 text-center text-slate-400">
                <span class="material-symbols-outlined text-[40px] text-slate-300 block mb-2">assignment_late</span>
                <p class="text-sm font-semibold">Belum ada proyek pengembangan yang tercatat.</p>
                <a href="{{ route('aptika.project.create') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-[#0D47A1] hover:underline">
                    <span>Inisiasi proyek pertama sekarang</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        @endforelse
    </div>

    @if ($projects->hasPages())
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
