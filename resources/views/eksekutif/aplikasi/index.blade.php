@extends('layouts.eksekutif')

@section('title', 'Katalog Aplikasi Daerah OPD')
@section('page-title', 'Inventaris & Katalog Aplikasi Daerah')
@section('breadcrumb', 'Katalog Aplikasi')

@section('content')
<div class="space-y-6">

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <a href="{{ route('eksekutif.aplikasi.index') }}" class="p-4 rounded-xl border {{ !request('status') ? 'bg-[#0D47A1] text-white border-[#0D47A1] shadow-sm shadow-[#0D47A1]/20' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ !request('status') ? 'text-blue-200' : 'text-slate-400' }}">Seluruh Aplikasi</div>
            <div class="text-2xl font-black mt-0.5">{{ $counts['total'] }}</div>
        </a>
        <a href="{{ route('eksekutif.aplikasi.index', ['status' => 'aktif']) }}" class="p-4 rounded-xl border {{ request('status') === 'aktif' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ request('status') === 'aktif' ? 'text-emerald-200' : 'text-slate-400' }}">Aplikasi Aktif</div>
            <div class="text-2xl font-black mt-0.5">{{ $counts['aktif'] }}</div>
        </a>
        <a href="{{ route('eksekutif.aplikasi.index', ['status' => 'dalam_pengembangan']) }}" class="p-4 rounded-xl border {{ request('status') === 'dalam_pengembangan' ? 'bg-amber-500 text-slate-950 border-amber-500 shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ request('status') === 'dalam_pengembangan' ? 'text-slate-900' : 'text-slate-400' }}">Tahap Pengembangan</div>
            <div class="text-2xl font-black mt-0.5">{{ $counts['dalam_pengembangan'] }}</div>
        </a>
        <a href="{{ route('eksekutif.aplikasi.index', ['status' => 'tidak_aktif']) }}" class="p-4 rounded-xl border {{ request('status') === 'tidak_aktif' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }} transition">
            <div class="text-[10px] uppercase font-bold tracking-wider {{ request('status') === 'tidak_aktif' ? 'text-rose-200' : 'text-slate-400' }}">Nonaktif / Dihentikan</div>
            <div class="text-2xl font-black mt-0.5">{{ $counts['tidak_aktif'] }}</div>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('eksekutif.aplikasi.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative flex-1 min-w-[240px]">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama aplikasi atau deskripsi..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/10 focus:border-[#0D47A1]">
            </div>

            <select name="instansi_id" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/10 focus:border-[#0D47A1] cursor-pointer">
                <option value="">Semua Perangkat Daerah (OPD)</option>
                @foreach ($instansis as $inst)
                    <option value="{{ $inst->id }}" {{ request('instansi_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->nama_instansi }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#165eae] transition shadow-xs cursor-pointer">
                Cari
            </button>

            @if (request('search') || request('instansi_id') || request('status'))
                <a href="{{ route('eksekutif.aplikasi.index') }}" class="px-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Grid Katalog Aplikasi -->
    @if ($aplikasis->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[30px]">apps_outage</span>
            </div>
            <h4 class="text-sm font-bold text-slate-800">Tidak Ada Aplikasi Ditemukan</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                Silakan ubah kriteria pencarian atau pilih instansi perangkat daerah lainnya.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($aplikasis as $app)
                <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between hover:border-slate-300 transition">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <span class="text-[10px] font-bold tracking-wider uppercase text-slate-400">
                                {{ $app->instansi->singkatan ?? ($app->instansi->nama_instansi ?? 'OPD Riau') }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                @if ($app->status === 'aktif') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif ($app->status === 'dalam_pengembangan') bg-amber-50 text-amber-700 border border-amber-200
                                @else bg-slate-100 text-slate-600 border border-slate-200
                                @endif">
                                {{ str_replace('_', ' ', $app->status) }}
                            </span>
                        </div>

                        <h3 class="text-sm font-bold text-slate-900 leading-snug">
                            {{ $app->nama_aplikasi }}
                        </h3>

                        <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                            {{ $app->deskripsi ?? 'Aplikasi pendukung layanan digital Pemerintah Provinsi Riau.' }}
                        </p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-400 truncate max-w-[170px]" title="{{ $app->instansi->nama_instansi ?? '-' }}">
                            {{ $app->instansi->nama_instansi ?? '-' }}
                        </span>

                        @if ($app->project)
                            <a href="{{ route('eksekutif.project.show', $app->project) }}" class="font-bold text-[#0D47A1] hover:text-[#165eae] flex items-center gap-1 transition">
                                <span>Lihat Proyek</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic text-[11px]">Tidak ada proyek aktif</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($aplikasis->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                {{ $aplikasis->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
