@extends('layouts.admin')

@section('title', 'Master Data Aplikasi')
@section('page-title', 'Master Data Aplikasi')
@section('breadcrumb', 'Master Data / Direktori Aplikasi')

@section('content')
<div class="space-y-6">
    <!-- Filter and Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('superadmin.aplikasi.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-3xl w-full">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama aplikasi atau deskripsi..." 
                    class="block w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
                >
            </div>

            <!-- Instansi Filter -->
            <div class="w-56">
                <select name="instansi_id" onchange="this.form.submit()" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer truncate">
                    <option value="">Semua Instansi OPD</option>
                    @foreach ($instansis as $ins)
                        <option value="{{ $ins->id }}" {{ $instansiFilter == $ins->id ? 'selected' : '' }}>
                            {{ $ins->nama_instansi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-44">
                <select name="status" onchange="this.form.submit()" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="pengembangan" {{ $statusFilter === 'pengembangan' ? 'selected' : '' }}>Pengembangan</option>
                    <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            @if ($search || $instansiFilter || $statusFilter)
                <a href="{{ route('superadmin.aplikasi.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>

        <!-- Add Button -->
        <a href="{{ route('superadmin.aplikasi.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Daftarkan Aplikasi</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Nama Aplikasi</th>
                        <th class="py-3.5 px-6">Instansi OPD Pemilik</th>
                        <th class="py-3.5 px-6">URL / Domain</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Tanggal Entri</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($aplikasis as $app)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $app->nama_aplikasi }}</div>
                                <div class="text-xs text-slate-400 max-w-sm truncate">{{ $app->deskripsi ?? 'Tanpa deskripsi' }}</div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-700">
                                {{ $app->instansi->nama_instansi ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-xs font-mono">
                                @if ($app->url_aplikasi)
                                    <a href="{{ $app->url_aplikasi }}" target="_blank" rel="noopener noreferrer" class="text-[#0D47A1] hover:underline inline-flex items-center gap-1">
                                        <span class="truncate max-w-[180px]">{{ $app->url_aplikasi }}</span>
                                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                    </a>
                                @else
                                    <span class="text-slate-300 italic">Belum di-hosting</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if ($app->status === 'aktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @elseif ($app->status === 'pengembangan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Pengembangan</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400">
                                {{ $app->created_at->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.aplikasi.edit', $app) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0D47A1] hover:bg-slate-100 transition" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.aplikasi.destroy', $app) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aplikasi {{ $app->nama_aplikasi }}?')" class="inline">
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
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">apps_outage</span>
                                <p class="text-sm font-semibold">Tidak ada aplikasi yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($aplikasis->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $aplikasis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
