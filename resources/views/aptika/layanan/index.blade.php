@extends('layouts.aptika')

@section('title', 'Manajemen Layanan')
@section('page-title', 'Manajemen Layanan Fasilitasi')
@section('breadcrumb', 'Konfigurasi Alur Layanan / Daftar Layanan')

@section('content')
<div class="space-y-6">
    <!-- Header with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Katalog Layanan Aplikasi Daerah</h2>
            <p class="text-xs text-slate-500 mt-1">
                Kelola jenis layanan fasilitasi yang tersedia bagi OPD. Setiap layanan memiliki alur tahapan (workflow) kustom.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can('layanan.create')
                <a href="{{ route('aptika.layanan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Tambah Layanan Baru</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Search Bar -->
    <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('aptika.layanan.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama atau deskripsi layanan..." 
                    class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>
            @if ($search)
                <a href="{{ route('aptika.layanan.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl transition">
                    Reset
                </a>
            @endif
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($layanans as $layanan)
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex flex-col justify-between overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-xs" style="background-color: {{ $layanan->warna }}15; color: {{ $layanan->warna }};">
                            <span class="material-symbols-outlined text-[28px]">{{ $layanan->ikon ?? 'category' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Toggle Active Form -->
                            <form method="POST" action="{{ route('aptika.layanan.toggle', $layanan) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="Klik untuk ubah status" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold cursor-pointer transition {{ $layanan->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $layanan->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $layanan->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 leading-snug">
                        {{ $layanan->nama }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                        {{ $layanan->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>

                    <!-- Badges Stats -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <span class="material-symbols-outlined text-[16px] text-blue-600">account_tree</span>
                            <span class="font-semibold">{{ $layanan->workflows_count }}</span>
                            <span class="text-slate-400">tahapan</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <span class="material-symbols-outlined text-[16px] text-purple-600">folder_open</span>
                            <span class="font-semibold">{{ $layanan->pengajuans_count }}</span>
                            <span class="text-slate-400">usulan</span>
                        </div>
                    </div>
                </div>

                <!-- Card Action Footer -->
                <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('aptika.layanan.show', $layanan) }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#0D47A1] hover:underline">
                        <span>Susun Alur ({{ $layanan->workflows_count }})</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>

                    <div class="flex items-center gap-1">
                        @can('layanan.edit')
                            <a href="{{ route('aptika.layanan.edit', $layanan) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 transition" title="Edit Layanan">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                        @endcan
                        @can('layanan.delete')
                            @if ($layanan->pengajuans_count === 0)
                                <form method="POST" action="{{ route('aptika.layanan.destroy', $layanan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Hapus Layanan">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200/80">
                <span class="material-symbols-outlined text-[48px] text-slate-300 block mb-2">category</span>
                <p class="text-sm font-bold text-slate-700">Belum ada layanan yang terdaftar.</p>
                <p class="text-xs text-slate-400 mt-1">Tambahkan layanan baru untuk mulai menyusun alur fasilitasi aplikasi OPD.</p>
                @can('layanan.create')
                    <a href="{{ route('aptika.layanan.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] transition">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        <span>Buat Layanan Pertama</span>
                    </a>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($layanans->hasPages())
        <div class="mt-4">
            {{ $layanans->links() }}
        </div>
    @endif
</div>
@endsection
