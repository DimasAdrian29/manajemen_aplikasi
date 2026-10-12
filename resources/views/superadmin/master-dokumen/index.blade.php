@extends('layouts.admin')

@section('title', 'Master Dokumen Regulasi')
@section('page-title', 'Master Dokumen Pengajuan')
@section('breadcrumb', 'Konfigurasi Syarat Berkas & Regulasi Pengajuan')

@section('content')
<div class="space-y-6">
    <!-- Quick Notice -->
    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-start gap-3.5">
        <span class="material-symbols-outlined text-blue-600 text-[24px] shrink-0 mt-0.5">info</span>
        <div class="text-xs text-slate-700 leading-relaxed">
            <span class="font-semibold text-blue-950">Fleksibilitas Regulasi & Peraturan Daerah:</span>
            Daftar dokumen di bawah ini secara dinamis menentukan kolom unggahan yang wajib/opsional diisi oleh OPD saat mengajukan permohonan. Anda dapat menambah syarat berkas baru, mengunggah format template blangko resmi, atau menonaktifkan dokumen sewaktu-waktu sesuai ketentuan terbaru.
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('superadmin.master-dokumen.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-xl w-full">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama dokumen atau kode..." 
                    class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
                >
            </div>

            <select 
                name="berlaku_untuk" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
            >
                <option value="">Semua Kategori Permohonan</option>
                <option value="semua" {{ $berlakuUntuk === 'semua' ? 'selected' : '' }}>Berlaku Semua Jenis</option>
                <option value="new_app" {{ $berlakuUntuk === 'new_app' ? 'selected' : '' }}>Aplikasi Baru</option>
                <option value="app_development" {{ $berlakuUntuk === 'app_development' ? 'selected' : '' }}>Pengembangan Aplikasi</option>
                <option value="app_deactivation" {{ $berlakuUntuk === 'app_deactivation' ? 'selected' : '' }}>Penonaktifan Aplikasi</option>
            </select>

            @if ($search || $berlakuUntuk)
                <a href="{{ route('superadmin.master-dokumen.index') }}" class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-700 font-medium">
                    Reset
                </a>
            @endif

            <button type="submit" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                Filter
            </button>
        </form>

        <!-- Add Button -->
        <a href="{{ route('superadmin.master-dokumen.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Tambah Jenis Dokumen</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-14">Urutan</th>
                        <th class="py-3.5 px-6">Nama & Kode Dokumen</th>
                        <th class="py-3.5 px-6">Berlaku Untuk</th>
                        <th class="py-3.5 px-6 text-center">Sifat</th>
                        <th class="py-3.5 px-6">Format / Batas</th>
                        <th class="py-3.5 px-6 text-center">Template Blangko</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($dokumens as $item)
                        <tr class="hover:bg-slate-50/60 transition {{ !$item->is_active ? 'opacity-60 bg-slate-50/30' : '' }}">
                            <td class="py-4 px-4 text-center font-mono text-xs font-semibold text-slate-400">
                                #{{ $item->urutan }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $item->nama_dokumen }}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-[11px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $item->kode_dokumen }}
                                    </span>
                                    @if ($item->deskripsi)
                                        <span class="text-xs text-slate-400 truncate max-w-xs" title="{{ $item->deskripsi }}">
                                            {{ $item->deskripsi }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($item->berlaku_untuk === 'semua')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                        Semua Permohonan
                                    </span>
                                @elseif ($item->berlaku_untuk === 'new_app')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aplikasi Baru
                                    </span>
                                @elseif ($item->berlaku_untuk === 'app_development')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        Pengembangan
                                    </span>
                                @elseif ($item->berlaku_untuk === 'app_deactivation')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Penonaktifan
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($item->wajib)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Wajib
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Opsional
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <div class="font-medium uppercase">{{ $item->format_diizinkan }}</div>
                                <div class="text-slate-400 text-[11px]">Maks. {{ number_format($item->max_size_kb / 1024, 1) }} MB</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($item->file_template)
                                    <a href="{{ asset('storage/' . $item->file_template) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition" title="Unduh Blangko Template">
                                        <span class="material-symbols-outlined text-[14px]">download</span>
                                        <span>Unduh</span>
                                    </a>
                                @else
                                    <span class="text-slate-300 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <form method="POST" action="{{ route('superadmin.master-dokumen.toggle', $item) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}" title="Klik untuk ubah status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.master-dokumen.edit', $item) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0D47A1] hover:bg-slate-100 transition" title="Edit Dokumen">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.master-dokumen.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis dokumen {{ $item->nama_dokumen }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Dokumen">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">folder_off</span>
                                <p class="text-sm font-semibold">Belum ada master jenis dokumen ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dokumens->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $dokumens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
