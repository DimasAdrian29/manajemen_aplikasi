@extends(auth()->user()->userLayout())

@section('title', 'Antrean Tugas Alur Kerja')
@section('page-title', 'Antrean Tugas Alur')
@section('breadcrumb', 'Alur Kerja / Antrean Tugas')

@section('content')
<div class="space-y-6">

    <!-- Header & Info Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0D47A1] text-2xl">task_alt</span>
                <h2 class="text-base font-bold text-slate-900">Antrean Tugas Alur Kerja Aktif</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Daftar proyek aplikasi yang saat ini berada pada tahapan alur yang ditugaskan kepada peran Anda 
                <span class="font-semibold text-slate-700">({{ auth()->user()->roles->pluck('name')->implode(', ') }})</span>.
            </p>
        </div>

        @if($isSuperadmin)
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-xs font-semibold">
                <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                <span>Mode Superadmin (Semua Antrean Tahapan)</span>
            </div>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('workflow.task.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari berdasarkan nama proyek, nama aplikasi, atau tahapan alur..." 
                    class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>
            @if(request('search'))
                <a href="{{ route('workflow.task.index') }}" class="px-3.5 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-xl transition">
                    Reset
                </a>
            @endif
            <button type="submit" class="px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Table of Tasks -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Proyek & Instansi</th>
                        <th class="py-3.5 px-6">Layanan</th>
                        <th class="py-3.5 px-6">Tahapan Aktif Saat Ini</th>
                        <th class="py-3.5 px-6 text-center">Peran Penanggung Jawab</th>
                        <th class="py-3.5 px-6 text-center">Progres Total</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($projects as $project)
                        @php
                            $activeWf = $project->currentWorkflow?->workflow;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6">
                                <a href="{{ route('workflow.task.show', $project) }}" class="text-xs font-bold text-slate-900 hover:text-[#0D47A1] transition block">
                                    {{ $project->nama_project }}
                                </a>
                                <span class="text-[11px] text-slate-400 block mt-0.5">
                                    {{ $project->aplikasi?->instansi?->nama_instansi ?? 'Instansi Pemohon' }}
                                    @if($project->pengajuan)
                                        • <span class="font-mono text-slate-500">{{ $project->pengajuan->kode_pengajuan }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($project->pengajuan?->layanan)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: {{ $project->pengajuan->layanan->warna }}15; color: {{ $project->pengajuan->layanan->warna }};">
                                        {{ $project->pengajuan->layanan->nama }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($activeWf)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[16px]">{{ $activeWf->ikon ?? 'account_tree' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-800 block">{{ $activeWf->nama }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $activeWf->fields->count() }} parameter isian</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum diset</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($activeWf?->role)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="material-symbols-outlined text-[13px]">badge</span>
                                        <span>{{ ucwords(str_replace('_', ' ', $activeWf->role->name)) }}</span>
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Terbuka untuk Umum</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="w-24 mx-auto">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-600 mb-1">
                                        <span>{{ $project->progres_total }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-[#0D47A1] h-1.5 rounded-full transition-all duration-300" style="width: {{ $project->progres_total }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('workflow.task.show', $project) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] shadow-xs transition">
                                    <span>Proses Tahapan</span>
                                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <span class="material-symbols-outlined text-[28px]">done_all</span>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Semua Tugas Alur Telah Selesai</p>
                                <p class="text-xs text-slate-400 mt-1">Saat ini tidak ada tahapan alur yang menunggu verifikasi dari peran Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($projects->hasPages())
        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    @endif

</div>
@endsection
