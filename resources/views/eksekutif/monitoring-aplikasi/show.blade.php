@extends('layouts.eksekutif')

@section('title', $aplikasi->nama_aplikasi)
@section('page-title', 'Detail Aplikasi')
@section('breadcrumb', 'Monitoring Aplikasi / Detail')

@php
    use App\Models\InventarisAplikasi;

    $statusBadge = [
        'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'tidak_aktif' => 'bg-rose-50 text-rose-700 border-rose-200',
        'diganti' => 'bg-amber-50 text-amber-700 border-amber-200',
    ];
    $sectionClass = 'bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden';
    $detail = [
        'Pengelola (OPD)' => $aplikasi->instansi?->nama_instansi,
        'PIC Aplikasi' => $aplikasi->pic_aplikasi,
        'Platform' => InventarisAplikasi::PLATFORM[$aplikasi->platform] ?? null,
        'Jenis Layanan' => InventarisAplikasi::JENIS_LAYANAN[$aplikasi->jenis_layanan] ?? null,
        'Kategori Konektivitas' => InventarisAplikasi::KONEKTIVITAS[$aplikasi->konektivitas] ?? null,
        'Tanggal Pemberlakuan' => $aplikasi->tanggal_pemberlakuan?->translatedFormat('d F Y'),
        'Penempatan Server' => $aplikasi->penempatan_server,
        'Aplikasi Pemprov Riau' => $aplikasi->is_aplikasi_pemprov ? 'Ya' : 'Tidak',
        'Website Resmi OPD/UPT' => $aplikasi->is_website_resmi ? 'Ya' : 'Tidak',
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                @if ($aplikasi->nomor_registrasi)
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-bold">No. {{ $aplikasi->nomor_registrasi }}</span>
                @endif
                <span class="px-2 py-0.5 rounded-full border text-[11px] font-bold {{ $statusBadge[$aplikasi->status] ?? '' }}">{{ $aplikasi->status_label }}</span>
                @if ($aplikasi->status_pengembangan)
                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[11px] font-bold">{{ $aplikasi->status_pengembangan_label }}</span>
                @endif
            </div>
            <h2 class="text-xl font-bold text-slate-900 leading-snug">{{ $aplikasi->nama_aplikasi }}</h2>
            @if ($aplikasi->url)
                <a href="{{ $aplikasi->url_lengkap }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs text-[#0D47A1] hover:underline mt-1 break-all">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>{{ $aplikasi->url }}
                </a>
            @endif
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('eksekutif.monitoring-aplikasi.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
            @can('inventaris.edit')
                <a href="{{ route('eksekutif.monitoring-aplikasi.edit', $aplikasi) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    <span>Ubah</span>
                </a>
            @endcan
            @can('inventaris.delete')
                <form method="POST" action="{{ route('eksekutif.monitoring-aplikasi.destroy', $aplikasi) }}" onsubmit="return confirm('Hapus aplikasi ini dari daftar monitoring?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span>Hapus</span>
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if ($aplikasi->status === 'diganti' && $aplikasi->aplikasiPengganti)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-amber-600 text-[20px]">swap_horiz</span>
            <span>Aplikasi ini telah diganti oleh
                <a href="{{ route('eksekutif.monitoring-aplikasi.show', $aplikasi->aplikasiPengganti) }}" class="font-bold underline">{{ $aplikasi->aplikasiPengganti->nama_aplikasi }}</a>.
            </span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Umum -->
            <div class="{{ $sectionClass }}">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">info</span>
                    <h3 class="text-sm font-bold text-slate-900">Informasi Umum</h3>
                </div>
                <dl class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @foreach ($detail as $label => $nilai)
                        <div>
                            <dt class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</dt>
                            <dd class="text-xs font-semibold text-slate-800 mt-1">{{ $nilai ?: '-' }}</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="px-6 pb-6 space-y-4">
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Deskripsi</div>
                        <p class="text-xs text-slate-700 mt-1 leading-relaxed whitespace-pre-line">{{ $aplikasi->deskripsi ?: '-' }}</p>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Fungsi / Menu</div>
                        <p class="text-xs text-slate-700 mt-1 leading-relaxed whitespace-pre-line">{{ $aplikasi->fungsi_menu ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tim Teknis -->
            <div class="{{ $sectionClass }}">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">engineering</span>
                    <h3 class="text-sm font-bold text-slate-900">Tim Teknis</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach (InventarisAplikasi::TIM_TEKNIS as $kolom => $label)
                        <div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ $label }}</div>
                            @forelse ($aplikasi->anggotaTim($kolom) as $nama)
                                <div class="flex items-center gap-2 text-xs text-slate-700 py-0.5">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">person</span>{{ $nama }}
                                </div>
                            @empty
                                <div class="text-xs text-slate-300">Belum diisi</div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Dokumentasi -->
            <div class="{{ $sectionClass }}">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">folder_open</span>
                        <h3 class="text-sm font-bold text-slate-900">Dokumentasi Administrasi</h3>
                    </div>
                    <span class="text-xs font-bold text-slate-500">{{ $aplikasi->jumlahDokumenLengkap() }}/{{ count(InventarisAplikasi::DOKUMEN) }}</span>
                </div>
                <ul class="p-4 space-y-2">
                    @foreach (InventarisAplikasi::DOKUMEN as $kolom => $label)
                        <li class="flex items-center justify-between px-3 py-2 rounded-xl {{ $aplikasi->{$kolom} ? 'bg-emerald-50' : 'bg-slate-50' }}">
                            <span class="text-xs font-semibold text-slate-700">{{ $label }}</span>
                            @if ($aplikasi->{$kolom})
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>Ada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>Belum Ada
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Catatan -->
            <div class="{{ $sectionClass }}">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">sticky_note_2</span>
                    <h3 class="text-sm font-bold text-slate-900">Catatan</h3>
                </div>
                <p class="p-6 text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $aplikasi->catatan ?: 'Tidak ada catatan.' }}</p>
            </div>

            @if ($aplikasi->aplikasiDigantikan->isNotEmpty())
                <div class="{{ $sectionClass }}">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">history</span>
                        <h3 class="text-sm font-bold text-slate-900">Menggantikan Aplikasi</h3>
                    </div>
                    <ul class="p-4 space-y-1">
                        @foreach ($aplikasi->aplikasiDigantikan as $lama)
                            <li>
                                <a href="{{ route('eksekutif.monitoring-aplikasi.show', $lama) }}" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-[#0D47A1]">{{ $lama->nama_aplikasi }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="text-[11px] text-slate-400 px-1">
                Dicatat {{ $aplikasi->created_at?->translatedFormat('d M Y H:i') }} · Diperbarui {{ $aplikasi->updated_at?->diffForHumans() }}
            </div>
        </div>
    </div>
</div>
@endsection
