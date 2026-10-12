@extends('layouts.infrastruktur')

@section('title', 'Antrean Deployment')
@section('page_title', 'Antrean Deployment Aplikasi')
@section('page_subtitle', 'Daftar proyek aplikasi yang telah lulus uji fungsional dan pengujian keamanan, siap dialokasikan server dan hosting')

@section('content')
<div class="space-y-6">

    <!-- Information Card -->
    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">rocket_launch</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-amber-900">Alur Deployment Produksi</h3>
                <p class="text-xs text-amber-800/80 mt-0.5 leading-relaxed">
                    Aplikasi pada antrean ini berstatus <strong>Deployment</strong>. Mengisi spesifikasi server dan nama domain akan otomatis mengaktifkan status proyek menjadi <strong>LIVE</strong> (progres 100%), mengaktifkan status aplikasi publik, serta mengirim notifikasi ke OPD pemohon dan tim pengembang.
                </p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('infrastruktur.deployment.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama proyek, nama aplikasi, atau instansi..." class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-[#0D47A1] text-white text-xs font-bold rounded-xl hover:bg-[#1565C0] transition flex items-center justify-center gap-1.5 shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <span>Filter</span>
                </button>
                @if (request()->hasAny(['search']))
                    <a href="{{ route('infrastruktur.deployment.index') }}" class="px-3 py-2.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Projects -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Proyek & Aplikasi</th>
                        <th class="px-6 py-4">Instansi Pemohon</th>
                        <th class="px-6 py-4">Status Pengujian</th>
                        <th class="px-6 py-4">Ketua Tim</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($projects as $project)
                        @php
                            $ketua = $project->tim->where('peran', 'ketua_tim')->first()?->user;
                            $latestTest = $project->testingAplikasi->last();
                            $latestSec = $project->pengujianKeamanan->last();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $project->nama_project }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Aplikasi: <span class="font-medium text-slate-700">{{ $project->aplikasi?->nama_aplikasi ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $project->aplikasi?->instansi?->nama_instansi ?? 'Tidak tercatat' }}</div>
                                <div class="text-[11px] text-slate-400">Kode: {{ $project->pengajuan?->kode_pengajuan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 text-[11px]">
                                        <span class="w-2 h-2 rounded-full {{ $latestTest && $latestTest->status === 'lulus' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span class="font-medium text-slate-700">Uji Fungsional:</span>
                                        <span class="font-bold {{ $latestTest && $latestTest->status === 'lulus' ? 'text-emerald-700' : 'text-slate-500' }}">
                                            {{ $latestTest ? ucfirst($latestTest->status) : 'Selesai' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px]">
                                        <span class="w-2 h-2 rounded-full {{ $latestSec && $latestSec->status === 'lulus' ? 'bg-emerald-500' : 'bg-blue-400' }}"></span>
                                        <span class="font-medium text-slate-700">Uji Keamanan:</span>
                                        <span class="font-bold text-blue-700">
                                            {{ $latestSec ? ucfirst($latestSec->status) : 'Diverifikasi' }}
                                        </span>
                                    </div>
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
                                <a href="{{ route('infrastruktur.deployment.create', $project) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] shadow-xs transition">
                                    <span class="material-symbols-outlined text-[16px]">settings_ethernet</span>
                                    <span>Deploy Sekarang</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-[28px]">rocket_launch</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">Tidak Ada Antrean Deployment</h4>
                                <p class="text-xs text-slate-400 mt-1">Saat ini belum ada proyek baru yang siap dideploy atau pencarian tidak ditemukan.</p>
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
