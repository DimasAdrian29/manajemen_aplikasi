@extends('layouts.eksekutif')

@section('title', 'Monitoring Aplikasi')
@section('page-title', 'Monitoring Daftar Aplikasi')
@section('breadcrumb', 'Monitoring Aplikasi Pemprov Riau')

@php
    use App\Models\InventarisAplikasi;

    $statusBadge = [
        'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'tidak_aktif' => 'bg-rose-50 text-rose-700 border-rose-200',
        'diganti' => 'bg-amber-50 text-amber-700 border-amber-200',
    ];
    $pengembanganBadge = [
        'sudah_selesai' => 'bg-emerald-50 text-emerald-700',
        'on_progress' => 'bg-blue-50 text-blue-700',
        'menunggu_konfirmasi' => 'bg-amber-50 text-amber-700',
        'dibatalkan' => 'bg-slate-100 text-slate-500',
    ];
    $adaFilter = collect($filters)->filter()->isNotEmpty();
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Aplikasi Pemerintah Provinsi Riau</h2>
            <p class="text-xs text-slate-500 mt-1">
                Monitoring seluruh aplikasi yang dikembangkan Diskominfotik beserta tim teknis, status pengembangan, dan kelengkapan dokumentasi administrasi.
            </p>
        </div>
        @can('inventaris.create')
            <a href="{{ route('eksekutif.monitoring-aplikasi.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition shrink-0">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Tambah Aplikasi</span>
            </a>
        @endcan
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Total Aplikasi', 'value' => $ringkasan['total'], 'icon' => 'apps', 'color' => 'text-[#0D47A1] bg-blue-50', 'filter' => []],
            ['label' => 'Aktif', 'value' => $ringkasan['aktif'], 'icon' => 'check_circle', 'color' => 'text-emerald-600 bg-emerald-50', 'filter' => ['status' => 'aktif']],
            ['label' => 'Tidak Aktif', 'value' => $ringkasan['tidak_aktif'], 'icon' => 'cancel', 'color' => 'text-rose-600 bg-rose-50', 'filter' => ['status' => 'tidak_aktif']],
            ['label' => 'Diganti', 'value' => $ringkasan['diganti'], 'icon' => 'swap_horiz', 'color' => 'text-amber-600 bg-amber-50', 'filter' => ['status' => 'diganti']],
        ] as $kartu)
            <a href="{{ route('eksekutif.monitoring-aplikasi.index', $kartu['filter']) }}" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $kartu['color'] }}">
                    <span class="material-symbols-outlined text-[24px]">{{ $kartu['icon'] }}</span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900 leading-none">{{ $kartu['value'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">{{ $kartu['label'] }}</div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Layanan Publik', 'value' => $ringkasan['publik']],
            ['label' => 'Layanan Pemerintahan', 'value' => $ringkasan['pemerintahan']],
            ['label' => 'Website Resmi OPD/UPT', 'value' => $ringkasan['website_resmi']],
            ['label' => 'Dalam Pengembangan', 'value' => $ringkasan['dalam_pengembangan']],
        ] as $indikator)
            <div class="bg-white rounded-xl px-4 py-3 border border-slate-200/80 shadow-xs flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">{{ $indikator['label'] }}</span>
                <span class="text-sm font-bold text-slate-900">{{ $indikator['value'] }}</span>
            </div>
        @endforeach
    </div>

    <!-- Filter -->
    <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('eksekutif.monitoring-aplikasi.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="relative lg:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Cari nama aplikasi, URL, atau nama tim..."
                    class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>
            <select name="instansi_id" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                <option value="">Semua Pengelola</option>
                @foreach ($instansiList as $instansi)
                    <option value="{{ $instansi->id }}" @selected(($filters['instansi_id'] ?? '') == $instansi->id)>{{ $instansi->nama_instansi }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                <option value="">Semua Status</option>
                @foreach (InventarisAplikasi::STATUS as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="jenis_layanan" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                <option value="">Semua Jenis Layanan</option>
                @foreach (InventarisAplikasi::JENIS_LAYANAN as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['jenis_layanan'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status_pengembangan" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                <option value="">Semua Status Pengembangan</option>
                @foreach (InventarisAplikasi::STATUS_PENGEMBANGAN as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status_pengembangan'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 md:col-span-2 lg:col-span-6 justify-end">
                @if ($adaFilter)
                    <a href="{{ route('eksekutif.monitoring-aplikasi.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl transition">
                        Reset
                    </a>
                @endif
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold">No</th>
                        <th class="px-4 py-3 text-left font-bold">Aplikasi</th>
                        <th class="px-4 py-3 text-left font-bold">Pengelola</th>
                        <th class="px-4 py-3 text-left font-bold">Status</th>
                        <th class="px-4 py-3 text-left font-bold">Layanan</th>
                        <th class="px-4 py-3 text-left font-bold">Pengembangan</th>
                        <th class="px-4 py-3 text-center font-bold">Dokumen</th>
                        <th class="px-4 py-3 text-right font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($inventaris as $aplikasi)
                        <tr class="hover:bg-slate-50/60 align-top">
                            <td class="px-4 py-3 text-slate-400 font-semibold">{{ $aplikasi->nomor_registrasi ?? '-' }}</td>
                            <td class="px-4 py-3 min-w-64">
                                <a href="{{ route('eksekutif.monitoring-aplikasi.show', $aplikasi) }}" class="font-bold text-slate-900 hover:text-[#0D47A1] leading-snug">
                                    {{ $aplikasi->nama_aplikasi }}
                                </a>
                                @if ($aplikasi->url)
                                    <a href="{{ $aplikasi->url_lengkap }}" target="_blank" rel="noopener" class="flex items-center gap-1 text-[11px] text-[#0D47A1] hover:underline mt-0.5 break-all">
                                        <span class="material-symbols-outlined text-[14px]">link</span>{{ $aplikasi->url }}
                                    </a>
                                @endif
                                @if ($aplikasi->status === 'diganti' && $aplikasi->aplikasiPengganti)
                                    <div class="text-[11px] text-amber-700 mt-0.5">Diganti oleh: {{ $aplikasi->aplikasiPengganti->nama_aplikasi }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 min-w-48">{{ $aplikasi->instansi?->nama_instansi ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded-full border text-[11px] font-bold whitespace-nowrap {{ $statusBadge[$aplikasi->status] ?? '' }}">
                                    {{ $aplikasi->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                {{ InventarisAplikasi::JENIS_LAYANAN[$aplikasi->jenis_layanan] ?? '-' }}
                                @if ($aplikasi->platform)
                                    <div class="text-[11px] text-slate-400">{{ InventarisAplikasi::PLATFORM[$aplikasi->platform] ?? $aplikasi->platform }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($aplikasi->status_pengembangan)
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold whitespace-nowrap {{ $pengembanganBadge[$aplikasi->status_pengembangan] ?? '' }}">
                                        {{ $aplikasi->status_pengembangan_label }}
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-0.5" title="{{ $aplikasi->jumlahDokumenLengkap() }} dari {{ count(InventarisAplikasi::DOKUMEN) }} dokumen tersedia">
                                    @foreach (InventarisAplikasi::DOKUMEN as $kolom => $label)
                                        <span class="w-2 h-2 rounded-full {{ $aplikasi->{$kolom} ? 'bg-emerald-500' : 'bg-slate-200' }}" title="{{ $label }}: {{ $aplikasi->{$kolom} ? 'Ada' : 'Belum Ada' }}"></span>
                                    @endforeach
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">{{ $aplikasi->jumlahDokumenLengkap() }}/{{ count(InventarisAplikasi::DOKUMEN) }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('eksekutif.monitoring-aplikasi.show', $aplikasi) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0D47A1] hover:bg-blue-50 transition" title="Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    @can('inventaris.edit')
                                        <a href="{{ route('eksekutif.monitoring-aplikasi.edit', $aplikasi) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 transition" title="Edit">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    @endcan
                                    @can('inventaris.delete')
                                        <form method="POST" action="{{ route('eksekutif.monitoring-aplikasi.destroy', $aplikasi) }}" onsubmit="return confirm('Hapus aplikasi {{ addslashes($aplikasi->nama_aplikasi) }} dari daftar monitoring?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] text-slate-300 block mb-2">apps</span>
                                <p class="text-sm font-bold text-slate-700">{{ $adaFilter ? 'Tidak ada aplikasi yang cocok dengan filter.' : 'Belum ada aplikasi yang tercatat.' }}</p>
                                <p class="text-xs text-slate-400 mt-1">Tambahkan data aplikasi untuk mulai memantau daftar aplikasi Pemprov Riau.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($inventaris->hasPages())
        <div class="mt-4">
            {{ $inventaris->links() }}
        </div>
    @endif
</div>
@endsection
