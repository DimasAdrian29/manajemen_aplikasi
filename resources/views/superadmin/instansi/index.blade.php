@extends('layouts.admin')

@section('title', 'Master Data Instansi OPD')
@section('page-title', 'Master Instansi OPD')
@section('breadcrumb', 'Manajemen Instansi dan Perangkat Daerah')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search Form -->
        <form method="GET" action="{{ route('superadmin.instansi.index') }}" class="relative max-w-md w-full">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                placeholder="Cari nama dinas atau email..." 
                class="block w-full pl-10 pr-24 py-2.5 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
            >
            @if ($search)
                <a href="{{ route('superadmin.instansi.index') }}" class="absolute inset-y-0 right-16 pr-2 flex items-center text-xs text-slate-400 hover:text-slate-600">
                    Reset
                </a>
            @endif
            <button type="submit" class="absolute inset-y-1 right-1 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                Cari
            </button>
        </form>

        <!-- Add Button -->
        <a href="{{ route('superadmin.instansi.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>Tambah Instansi Baru</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">No</th>
                        <th class="py-3.5 px-6">Nama Instansi OPD</th>
                        <th class="py-3.5 px-6">Kontak</th>
                        <th class="py-3.5 px-6">Alamat</th>
                        <th class="py-3.5 px-6 text-center">Data Terhubung</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($instansis as $index => $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 text-slate-400 font-mono text-xs">
                                {{ $instansis->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $item->nama_instansi }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <div>{{ $item->email ?? '-' }}</div>
                                <div class="text-slate-400">{{ $item->no_telepon ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500 max-w-xs truncate">
                                {{ $item->alamat ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0D47A1] border border-blue-100 mr-1" title="Jumlah Pengguna">
                                    <span class="material-symbols-outlined text-[14px]">group</span>
                                    <span>{{ $item->users_count }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100" title="Jumlah Aplikasi">
                                    <span class="material-symbols-outlined text-[14px]">apps</span>
                                    <span>{{ $item->aplikasi_count }}</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.instansi.edit', $item) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0D47A1] hover:bg-slate-100 transition" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.instansi.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus instansi {{ $item->nama_instansi }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">domain_disabled</span>
                                <p class="text-sm font-semibold">Belum ada data Instansi OPD ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($instansis->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $instansis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
