@extends('layouts.aptika')

@section('title', 'Uji Fungsional Sistem')
@section('page-title', 'Audit & Uji Fungsional Aplikasi')
@section('breadcrumb', 'Kendali Mutu / Uji Fungsional')

@section('content')
<div class="space-y-6">
    <!-- Notice Card -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100/80 shadow-xs flex items-start gap-4">
        <span class="material-symbols-outlined text-emerald-700 text-[28px] shrink-0 mt-0.5">verified</span>
        <div class="text-xs text-slate-700 leading-relaxed">
            <span class="font-bold text-emerald-950 block text-sm mb-1">Standar Kendali Mutu Aplikasi Informatika (APTIKA)</span>
            Uji Fungsional memverifikasi kesesuaian alur modul, peran pengguna, dan validasi formulir sistem sebelum aplikasi diteruskan ke Bidang Persandian untuk Audit Keamanan Informasi (*penetration test*).
        </div>
    </div>

    <!-- Quick Action & Ready Projects Card -->
    @if ($readyProjects->isNotEmpty())
        <div class="rounded-2xl bg-white border border-slate-200/80 p-6 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">notification_important</span>
                <span>Proyek Siap Masuk Uji Fungsional</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($readyProjects as $rp)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800">
                                {{ str_replace('_', ' ', $rp->status) }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-900 mt-2 line-clamp-1">{{ $rp->nama_project }}</h4>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $rp->aplikasi->instansi->nama_instansi ?? '-' }}</div>
                        </div>
                        <a href="{{ route('aptika.testing.create', ['project_id' => $rp->id]) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">fact_check</span>
                            <span>Mulai Pengujian</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Bar & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('aptika.testing.index') }}" class="flex items-center gap-3">
            <select 
                name="status_validasi" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
            >
                <option value="">Semua Hasil Validasi</option>
                <option value="lulus" {{ $statusValidasi === 'lulus' ? 'selected' : '' }}>Lulus (Siap Uji Keamanan)</option>
                <option value="perbaikan" {{ $statusValidasi === 'perbaikan' ? 'selected' : '' }}>Perbaikan Bug</option>
                <option value="tidak_lulus" {{ $statusValidasi === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
            </select>

            @if ($statusValidasi)
                <a href="{{ route('aptika.testing.index') }}" class="text-xs text-slate-500 hover:text-slate-700 font-semibold">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('aptika.testing.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-sm shadow-[#0D47A1]/20 transition shrink-0">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>Input Hasil Uji Fungsional</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Tanggal Uji</th>
                        <th class="py-3.5 px-6">Nama Proyek & Instansi</th>
                        <th class="py-3.5 px-6">Penguji APTIKA</th>
                        <th class="py-3.5 px-6 text-center">Status Validasi</th>
                        <th class="py-3.5 px-6">Catatan Audit</th>
                        <th class="py-3.5 px-6 text-right">Laporan Berkas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($testings as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 text-xs text-slate-600 font-medium whitespace-nowrap">
                                {{ $item->tanggal_testing?->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('aptika.project.show', $item->project) }}" class="font-bold text-slate-900 hover:text-[#0D47A1] transition">
                                    {{ $item->project->nama_project }}
                                </a>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $item->project->aplikasi->instansi->nama_instansi ?? '-' }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-700">
                                {{ $item->penguji->name ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($item->status_validasi === 'lulus')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Lulus</span>
                                    </span>
                                @elseif ($item->status_validasi === 'perbaikan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Perbaikan</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Tidak Lulus</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600 max-w-xs truncate" title="{{ $item->catatan_validasi }}">
                                {{ $item->catatan_validasi ?? ($item->keterangan ?? '-') }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                @if ($item->file_hasil)
                                    <a href="{{ asset('storage/' . $item->file_hasil) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#0D47A1] bg-blue-50 hover:bg-blue-100 transition">
                                        <span class="material-symbols-outlined text-[15px]">download</span>
                                        <span>Unduh Berkas</span>
                                    </a>
                                @else
                                    <span class="text-slate-300 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">assignment_turned_in</span>
                                <p class="text-sm font-semibold">Belum ada riwayat hasil uji fungsional yang dicatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($testings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $testings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
