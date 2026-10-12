@extends('layouts.opd')

@section('title', 'Riwayat Pengajuan')
@section('page-title', 'Riwayat Permohonan Layanan')
@section('breadcrumb', 'Layanan Aplikasi / Riwayat Pengajuan')

@section('content')
<div class="space-y-6">
    <!-- Action Bar & Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('opd.pengajuan.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-2xl w-full">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari kode registrasi, nama aplikasi..." 
                    class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
                >
            </div>

            <select 
                name="jenis" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
            >
                <option value="">Semua Layanan</option>
                <option value="new_app" {{ $jenis === 'new_app' ? 'selected' : '' }}>Aplikasi Baru</option>
                <option value="app_development" {{ $jenis === 'app_development' ? 'selected' : '' }}>Pengembangan</option>
                <option value="app_deactivation" {{ $jenis === 'app_deactivation' ? 'selected' : '' }}>Penonaktifan</option>
            </select>

            <select 
                name="status" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
            >
                <option value="">Semua Status</option>
                <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Terkirim</option>
                <option value="under_review" {{ $status === 'under_review' ? 'selected' : '' }}>Dalam Telaah</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>

            @if ($search || $jenis || $status)
                <a href="{{ route('opd.pengajuan.index') }}" class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-700 font-medium">
                    Reset
                </a>
            @endif

            <button type="submit" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                Filter
            </button>
        </form>

        <!-- New Submission Button -->
        <a href="{{ route('opd.pengajuan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Buat Permohonan Baru</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">No. Registrasi</th>
                        <th class="py-3.5 px-6">Layanan</th>
                        <th class="py-3.5 px-6">Nama Usulan / Sistem</th>
                        <th class="py-3.5 px-6">Narahubung</th>
                        <th class="py-3.5 px-6 text-center">Berkas Lampiran</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6">Tanggal</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($pengajuans as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-[#0D47A1]">
                                <a href="{{ route('opd.pengajuan.show', $item) }}" class="hover:underline">
                                    {{ $item->kode_pengajuan }}
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                @if ($item->jenis_pengajuan === 'new_app')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aplikasi Baru
                                    </span>
                                @elseif ($item->jenis_pengajuan === 'app_development')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        Pengembangan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Penonaktifan
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">
                                    {{ $item->nama_aplikasi ?? ($item->aplikasi->nama_aplikasi ?? '-') }}
                                </div>
                                <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5" title="{{ $item->deskripsi_kebutuhan }}">
                                    {{ $item->deskripsi_kebutuhan }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <div class="font-medium text-slate-800">{{ $item->nama_pemohon }}</div>
                                <div class="text-slate-400">{{ $item->no_hp_pemohon }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    <span class="material-symbols-outlined text-[14px]">attach_file</span>
                                    <span>{{ $item->dokumen->count() }} Dokumen</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($item->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Terkirim</span>
                                    </span>
                                @elseif ($item->status === 'under_review')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
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
                            <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('opd.pengajuan.show', $item) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#0D47A1] bg-blue-50 hover:bg-blue-100 transition">
                                        <span class="material-symbols-outlined text-[15px]">visibility</span>
                                        <span>Detail</span>
                                    </a>

                                    @if (in_array($item->status, ['draft', 'submitted']))
                                        <form method="POST" action="{{ route('opd.pengajuan.destroy', $item) }}" onsubmit="return confirm('Batalkan pengajuan {{ $item->kode_pengajuan }}? Seluruh berkas akan dihapus.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Batalkan Permohonan">
                                                <span class="material-symbols-outlined text-[17px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">folder_off</span>
                                <p class="text-sm font-semibold">Tidak ada riwayat permohonan yang sesuai kriteria.</p>
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
