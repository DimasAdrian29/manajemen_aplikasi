@extends('layouts.aptika')

@section('title', 'Master Workflow')
@section('page-title', 'Master Alur Kerja (Workflows)')
@section('breadcrumb', 'Konfigurasi Alur Layanan / Master Workflow')

@section('content')
<div class="space-y-6">
    <!-- Header with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Katalog Master Workflow</h2>
            <p class="text-xs text-slate-500 mt-1">
                Katalog tahapan alur kerja yang dapat dirangkai ke dalam berbagai jenis layanan fasilitasi aplikasi.
            </p>
        </div>
        <div>
            @can('workflow.create')
                <a href="{{ route('aptika.workflow.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Tambah Workflow Baru</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Search Bar -->
    <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('aptika.workflow.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama atau deskripsi workflow..." 
                    class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>
            @if ($search)
                <a href="{{ route('aptika.workflow.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl transition">
                    Reset
                </a>
            @endif
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Table of Workflows -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Tahapan Workflow</th>
                        <th class="py-3.5 px-6">Slug Kode</th>
                        <th class="py-3.5 px-6 text-center">Peran Pelaksana</th>
                        <th class="py-3.5 px-6 text-center">Kelengkapan Field</th>
                        <th class="py-3.5 px-6 text-center">Digunakan Oleh</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($workflows as $wf)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">{{ $wf->ikon ?? 'account_tree' }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('aptika.workflow.show', $wf) }}" class="text-xs font-bold text-slate-900 hover:text-[#0D47A1] transition block">
                                            {{ $wf->nama }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                                            {{ $wf->deskripsi ?? 'Tidak ada deskripsi.' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-500">
                                {{ $wf->slug }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($wf->role)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="material-symbols-outlined text-[13px]">badge</span>
                                        <span>{{ ucwords(str_replace('_', ' ', $wf->role->name)) }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] text-slate-400 bg-slate-50 italic">
                                        Umum / Fleksibel
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span>{{ $wf->fields_count }} field isian</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    <span>{{ $wf->layanan_workflows_count }} layanan</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('aptika.workflow.show', $wf) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-[#0D47A1] bg-blue-50 hover:bg-blue-100 transition">
                                        <span>Kelola Field</span>
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>

                                    @can('workflow.edit')
                                        <a href="{{ route('aptika.workflow.edit', $wf) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" title="Edit Master">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    @endcan

                                    @can('workflow.delete')
                                        @if ($wf->layanan_workflows_count === 0)
                                            <form method="POST" action="{{ route('aptika.workflow.destroy', $wf) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus workflow ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Hapus Master">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">account_tree</span>
                                <p class="text-sm font-semibold">Belum ada data master workflow.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($workflows->hasPages())
        <div class="mt-4">
            {{ $workflows->links() }}
        </div>
    @endif
</div>
@endsection
