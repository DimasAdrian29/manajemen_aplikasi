@extends('layouts.eksekutif')

@section('title', 'Portofolio Proyek Pengembangan')
@section('page-title', 'Portofolio & Pengawasan Proyek Daerah')
@section('breadcrumb', 'Portofolio Proyek')

@section('content')
<div class="space-y-6">

    <!-- Top Status Tabs & Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('eksekutif.project.index') }}" class="p-3.5 rounded-xl border {{ !request('status') ? 'bg-[#0D47A1] text-white border-[#0D47A1] shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ !request('status') ? 'text-blue-200' : 'text-slate-400' }}">Semua Proyek</div>
            <div class="text-xl font-black mt-0.5">{{ $counts['total'] }}</div>
        </a>
        <a href="{{ route('eksekutif.project.index', ['status' => 'in_development']) }}" class="p-3.5 rounded-xl border {{ request('status') === 'in_development' ? 'bg-[#0D47A1] text-white border-[#0D47A1] shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ request('status') === 'in_development' ? 'text-blue-200' : 'text-slate-400' }}">Pengembangan</div>
            <div class="text-xl font-black mt-0.5">{{ $counts['in_development'] }}</div>
        </a>
        <a href="{{ route('eksekutif.project.index', ['status' => 'functional_testing']) }}" class="p-3.5 rounded-xl border {{ in_array(request('status'), ['functional_testing', 'security_testing']) ? 'bg-teal-600 text-white border-teal-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ in_array(request('status'), ['functional_testing', 'security_testing']) ? 'text-teal-200' : 'text-slate-400' }}">Pengujian (QA/CSIRT)</div>
            <div class="text-xl font-black mt-0.5">{{ $counts['testing'] }}</div>
        </a>
        <a href="{{ route('eksekutif.project.index', ['status' => 'deployment']) }}" class="p-3.5 rounded-xl border {{ request('status') === 'deployment' ? 'bg-cyan-600 text-white border-cyan-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ request('status') === 'deployment' ? 'text-cyan-200' : 'text-slate-400' }}">Deployment</div>
            <div class="text-xl font-black mt-0.5">{{ $counts['deployment'] }}</div>
        </a>
        <a href="{{ route('eksekutif.project.index', ['status' => 'live']) }}" class="p-3.5 rounded-xl border {{ request('status') === 'live' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ request('status') === 'live' ? 'text-emerald-200' : 'text-slate-400' }}">Telah Go-Live</div>
            <div class="text-xl font-black mt-0.5">{{ $counts['live'] }}</div>
        </a>
        <a href="{{ route('eksekutif.project.index', ['status' => 'delayed']) }}" class="p-3.5 rounded-xl border {{ request('status') === 'delayed' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ request('status') === 'delayed' ? 'text-rose-200' : 'text-slate-400' }}">Tertunda / Kendala</div>
            <div class="text-xl font-black mt-0.5">{{ $counts['delayed'] }}</div>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('eksekutif.project.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative flex-1 min-w-[240px]">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama proyek atau nama aplikasi..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
            </div>

            <select name="instansi_id" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer">
                <option value="">Semua Perangkat Daerah (OPD)</option>
                @foreach ($instansis as $inst)
                    <option value="{{ $inst->id }}" {{ request('instansi_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->nama_instansi }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#165eae] transition">
                Filter
            </button>

            @if (request('search') || request('instansi_id') || request('status'))
                <a href="{{ route('eksekutif.project.index') }}" class="px-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        @if ($projects->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[30px]">folder_off</span>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Tidak Ada Proyek yang Sesuai</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                    Silakan ubah kata kunci pencarian atau sesuaikan opsi filter status proyek.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Nama Proyek & Target</th>
                            <th class="py-3.5 px-6">Perangkat Daerah (OPD)</th>
                            <th class="py-3.5 px-6">Ketua Tim Teknis</th>
                            <th class="py-3.5 px-6">Progres Pengerjaan</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($projects as $proj)
                            @php
                                $lead = $proj->tim->where('peran', 'ketua_tim')->first()?->user;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6">
                                    <a href="{{ route('eksekutif.project.show', $proj) }}" class="font-bold text-slate-900 hover:text-[#0D47A1] transition block">
                                        {{ $proj->nama_project }}
                                    </a>
                                    <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1.5 font-mono">
                                        <span class="material-symbols-outlined text-[14px]">event</span>
                                        <span>Target: {{ $proj->tanggal_selesai ? $proj->tanggal_selesai->format('d M Y') : 'Belum Ditentukan' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <div class="font-semibold text-slate-800">{{ $proj->aplikasi->instansi->nama_instansi ?? '-' }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $proj->aplikasi->nama_aplikasi }}</div>
                                </td>
                                <td class="py-4 px-6 text-xs">
                                    @if ($lead)
                                        <div class="font-bold text-slate-800">{{ $lead->name }}</div>
                                        <div class="text-slate-400 text-[10px]">{{ $lead->jabatan ?? 'Koordinator Tim' }}</div>
                                    @else
                                        <span class="text-slate-400 italic">Belum Ditugaskan</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 min-w-[170px]">
                                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                                        <span class="text-slate-700">{{ $proj->progres_total }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full transition-all duration-300 {{ $proj->progres_total >= 100 ? 'bg-emerald-500' : ($proj->progres_total > 50 ? 'bg-[#0D47A1]' : 'bg-amber-500') }}" style="width: {{ $proj->progres_total }}%"></div>
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
                                    <a href="{{ route('eksekutif.project.show', $proj) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                                        <span>Lembar Kerja</span>
                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($projects->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $projects->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
