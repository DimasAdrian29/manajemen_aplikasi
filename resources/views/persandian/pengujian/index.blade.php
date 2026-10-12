@extends('layouts.persandian')

@section('title', 'Antrean Uji Keamanan')
@section('page_title', 'Antrean Uji Keamanan Informasi')
@section('page_subtitle', 'Daftar aplikasi yang telah dinyatakan lulus uji fungsional oleh APTIKA dan siap diaudit kerentanannya oleh CSIRT Riau')

@section('content')
<div class="space-y-6">

    <!-- Explanatory Banner -->
    <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">verified_user</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-emerald-950">Tahapan Audit Keamanan Siber</h3>
                <p class="text-xs text-emerald-900/80 mt-0.5 leading-relaxed">
                    Aplikasi dalam antrean ini berstatus <strong>Security Testing</strong>. Meluluskan audit keamanan akan menerbitkan rekomendasi laik operasi dan otomatis meneruskan proyek ke antrean <strong>Deployment</strong> (Bidang Infrastruktur). Jika ditemukan celah, proyek dapat dikembalikan ke programmer untuk perbaikan.
                </p>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('persandian.pengujian.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama proyek, nama aplikasi, atau instansi pemohon..." class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-[#0D47A1] text-white text-xs font-bold rounded-xl hover:bg-[#1565C0] transition flex items-center justify-center gap-1.5 shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <span>Cari</span>
                </button>
                @if (request()->has('search'))
                    <a href="{{ route('persandian.pengujian.index') }}" class="px-3 py-2.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Projects Waiting for Security Audit -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Proyek & Aplikasi</th>
                        <th class="px-6 py-4">Instansi Pemohon</th>
                        <th class="px-6 py-4">Status Uji Fungsional</th>
                        <th class="px-6 py-4">Ketua Tim Dev</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($projects as $project)
                        @php
                            $ketua = $project->tim->where('peran', 'ketua_tim')->first()?->user;
                            $latestTest = $project->testingAplikasi->last();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $project->nama_project }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Aplikasi: <span class="font-medium text-slate-700">{{ $project->aplikasi?->nama_aplikasi ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $project->aplikasi?->instansi?->nama_instansi ?? 'Pemerintah Provinsi Riau' }}</div>
                                <div class="text-[11px] text-slate-400">Kode: {{ $project->pengajuan?->kode_pengajuan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="font-semibold text-emerald-800">Lulus Pengujian APTIKA</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Tgl: {{ $latestTest?->tanggal_testing?->format('d/m/Y') ?? 'Terverifikasi' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($ketua)
                                    <div class="font-semibold text-slate-800">{{ $ketua->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $ketua->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Belum di-assign</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('persandian.pengujian.create', $project) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition">
                                    <span class="material-symbols-outlined text-[16px]">security</span>
                                    <span>Audit Keamanan</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-[28px]">verified</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">Tidak Ada Antrean Uji Keamanan</h4>
                                <p class="text-xs text-slate-400 mt-1">Saat ini belum ada proyek berstatus security testing yang siap diaudit.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
