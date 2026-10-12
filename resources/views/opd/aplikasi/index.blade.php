@extends('layouts.opd')

@section('title', 'Katalog Aplikasi Kami')
@section('page-title', 'Katalog Aplikasi Milik Perangkat Daerah')
@section('breadcrumb', 'Katalog Aplikasi / Daftar Sistem')

@section('content')
<div class="space-y-6">
    <!-- Header Notice -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold text-slate-900">Direktori Aplikasi Resmi OPD</h3>
            <p class="text-xs text-slate-500 mt-0.5">Daftar sistem informasi milik {{ auth()->user()->instansi->nama_instansi ?? 'instansi Anda' }} yang tercatat di Pusat Data Diskominfo Riau.</p>
        </div>
        <a href="{{ route('opd.pengajuan.create', ['jenis' => 'new_app']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-semibold shadow-xs transition shrink-0">
            <span class="material-symbols-outlined text-[16px]">add_circle</span>
            <span>Usulkan Aplikasi Baru</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('opd.aplikasi.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-xl">
        <div class="relative flex-1 min-w-[200px]">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                placeholder="Cari nama atau deskripsi aplikasi..." 
                class="block w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
            >
        </div>

        <select 
            name="status" 
            onchange="this.form.submit()" 
            class="py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
        >
            <option value="">Semua Status</option>
            <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif Beroperasi</option>
            <option value="pengembangan" {{ $status === 'pengembangan' ? 'selected' : '' }}>Tahap Pengembangan</option>
            <option value="nonaktif" {{ $status === 'nonaktif' ? 'selected' : '' }}>Dinonaktifkan</option>
        </select>

        @if ($search || $status)
            <a href="{{ route('opd.aplikasi.index') }}" class="px-2.5 py-2 text-xs text-slate-500 hover:text-slate-700 font-medium">
                Reset
            </a>
        @endif
    </form>

    <!-- Application Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($aplikasis as $app)
            <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center font-bold text-sm shrink-0">
                            <span class="material-symbols-outlined text-[22px]">laptop_mac</span>
                        </div>
                        @if ($app->status === 'aktif')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Aktif</span>
                            </span>
                        @elseif ($app->status === 'pengembangan')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                <span>Pengembangan</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>Nonaktif</span>
                            </span>
                        @endif
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 line-clamp-1" title="{{ $app->nama_aplikasi }}">
                        {{ $app->nama_aplikasi }}
                    </h4>
                    
                    <p class="text-xs text-slate-500 mt-1.5 line-clamp-3 leading-relaxed">
                        {{ $app->deskripsi ?? 'Belum ada penjelasan deskripsi sistem.' }}
                    </p>

                    @if ($app->url_aplikasi)
                        <div class="mt-3">
                            <a href="{{ $app->url_aplikasi }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#0D47A1] hover:underline truncate max-w-full">
                                <span class="material-symbols-outlined text-[14px]">link</span>
                                <span class="truncate">{{ $app->url_aplikasi }}</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('opd.pengajuan.create', ['jenis' => 'app_development']) }}" class="flex-1 py-1.5 px-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#0D47A1] text-center text-xs font-semibold transition" title="Ajukan pengembangan fitur pada aplikasi ini">
                        Ajukan Fitur Baru
                    </a>
                    <a href="{{ route('opd.pengajuan.create', ['jenis' => 'app_deactivation']) }}" class="py-1.5 px-2 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 text-xs font-semibold transition" title="Ajukan penonaktifan aplikasi ini">
                        Penonaktifan
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl bg-white border border-slate-200/80 p-12 text-center text-slate-400">
                <span class="material-symbols-outlined text-[40px] text-slate-300 block mb-2">devices_off</span>
                <p class="text-sm font-semibold">Belum ada aplikasi yang terdaftar untuk perangkat daerah Anda.</p>
                <p class="text-xs text-slate-400 mt-1">Anda dapat memulai dengan mengajukan permohonan pembangunan aplikasi baru.</p>
                <a href="{{ route('opd.pengajuan.create', ['jenis' => 'new_app']) }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold">
                    <span class="material-symbols-outlined text-[16px]">add_circle</span>
                    <span>Ajukan Aplikasi Baru</span>
                </a>
            </div>
        @endforelse
    </div>

    @if ($aplikasis->hasPages())
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80">
            {{ $aplikasis->links() }}
        </div>
    @endif
</div>
@endsection
