@extends('layouts.aptika')

@section('title', 'Telaah Usulan OPD')
@section('page-title', 'Telaah Usulan Pengajuan OPD')
@section('breadcrumb', 'Manajemen Layanan / Telaah Pengajuan')

@section('content')
<div class="space-y-6">
    <!-- Filter and Search Bar -->
    <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
        <form method="GET" action="{{ route('aptika.pengajuan.index') }}" class="flex flex-wrap items-center gap-3">
            <!-- Search Text -->
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari kode, nama aplikasi, nama dinas..." 
                    class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>

            <!-- Filter Instansi -->
            <select 
                name="instansi_id" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer"
            >
                <option value="">Semua Perangkat Daerah (OPD)</option>
                @foreach ($instansis as $ins)
                    <option value="{{ $ins->id }}" {{ $instansiId == $ins->id ? 'selected' : '' }}>
                        {{ $ins->nama_instansi }}
                    </option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select 
                name="status" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer"
            >
                <option value="">Semua Status</option>
                <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Baru Masuk (Submitted)</option>
                <option value="under_review" {{ $status === 'under_review' ? 'selected' : '' }}>Dalam Telaah</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak / Revisi</option>
            </select>

            <!-- Filter Layanan -->
            <select 
                name="layanan_id" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer"
            >
                <option value="">Semua Layanan</option>
                @foreach ($layanans as $lay)
                    <option value="{{ $lay->id }}" {{ $layananId == $lay->id ? 'selected' : '' }}>{{ $lay->nama }}</option>
                @endforeach
            </select>

            @if ($search || $status || $jenis || $instansiId)
                <a href="{{ route('aptika.pengajuan.index') }}" class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    Reset
                </a>
            @endif

            <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                Terapkan
            </button>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">No. Registrasi</th>
                        <th class="py-3.5 px-6">Perangkat Daerah (OPD)</th>
                        <th class="py-3.5 px-6">Layanan & Usulan Sistem</th>
                        <th class="py-3.5 px-6">Narahubung</th>
                        <th class="py-3.5 px-6 text-center">Berkas</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($pengajuans as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-[#0D47A1]">
                                <a href="{{ route('aptika.pengajuan.show', $item) }}" class="hover:underline">
                                    {{ $item->kode_pengajuan }}
                                </a>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900 max-w-xs">
                                <div>{{ $item->instansi->nama_instansi }}</div>
                                <div class="text-[11px] text-slate-400 font-normal mt-0.5">
                                    {{ $item->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">
                                    {{ $item->nama_aplikasi ?? ($item->aplikasi->nama_aplikasi ?? '-') }}
                                </div>
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $item->nama_layanan }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <div class="font-medium text-slate-800">{{ $item->nama_pemohon }}</div>
                                <div class="text-slate-400 font-mono">{{ $item->no_hp_pemohon }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    <span class="material-symbols-outlined text-[14px]">attach_file</span>
                                    <span>{{ $item->dokumen->count() }} Dokumen</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($item->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Baru Masuk</span>
                                    </span>
                                @elseif ($item->status === 'under_review')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Dalam Telaah</span>
                                    </span>
                                @elseif ($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Disetujui</span>
                                    </span>
                                @elseif ($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Ditolak</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('aptika.pengajuan.show', $item) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#165eae] shadow-xs transition">
                                        <span class="material-symbols-outlined text-[15px]">rate_review</span>
                                        <span>Telaah</span>
                                    </a>

                                    @if ($item->status === 'approved' && !$item->project)
                                        <a href="{{ route('aptika.project.create', ['pengajuan_id' => $item->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition" title="Inisiasi Proyek Pengembangan">
                                            <span class="material-symbols-outlined text-[15px]">assignment_add</span>
                                            <span>Inisiasi Proyek</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">folder_off</span>
                                <p class="text-sm font-semibold">Tidak ada usulan pengajuan yang sesuai dengan kriteria filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengajuans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
