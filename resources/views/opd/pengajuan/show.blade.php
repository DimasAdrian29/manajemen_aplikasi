@extends('layouts.opd')

@section('title', 'Detail Permohonan ' . $pengajuan->kode_pengajuan)
@section('page-title', 'Pelacakan Status Permohonan')
@section('breadcrumb', 'Layanan Aplikasi / ' . $pengajuan->kode_pengajuan)

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                <span class="font-mono text-sm font-extrabold text-[#0D47A1] bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                    {{ $pengajuan->kode_pengajuan }}
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $pengajuan->nama_layanan }}
                </span>
                @if ($pengajuan->status === 'submitted')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Terkirim</span>
                    </span>
                @elseif ($pengajuan->status === 'under_review')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                        <span>Dalam Telaah Teknis</span>
                    </span>
                @elseif ($pengajuan->status === 'approved')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Disetujui Kominfo</span>
                    </span>
                @elseif ($pengajuan->status === 'rejected')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span>Ditolak / Revisi</span>
                    </span>
                @endif
            </div>

            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                {{ $pengajuan->nama_aplikasi ?? ($pengajuan->aplikasi->nama_aplikasi ?? 'Usulan Aplikasi') }}
            </h2>
            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                <span>Diajukan pada: <strong>{{ $pengajuan->created_at->format('d F Y, H:i') }} WIB</strong></span>
                <span>•</span>
                <span>Oleh: <strong>{{ $pengajuan->nama_pemohon }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('opd.pengajuan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Tanda Terima</span>
            </button>
        </div>
    </div>

    <!-- Dynamic Stepper Pipeline Workflow -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">
            Alur Tahapan Layanan SPBE: {{ $pengajuan->nama_layanan }}
        </h3>

        @if ($pengajuan->layanan && $pengajuan->layanan->workflows->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($pengajuan->layanan->workflows as $lw)
                    @php
                        $wfData = $pengajuan->workflowData->firstWhere('layanan_workflow_id', $lw->id);
                        $stepStatus = $wfData?->status ?? 'pending';
                    @endphp
                    <div class="flex items-start gap-3.5 p-4 rounded-xl border transition {{ $stepStatus === 'completed' ? 'bg-emerald-50/40 border-emerald-200' : ($stepStatus === 'in_progress' ? 'bg-blue-50/50 border-[#0D47A1]/30 ring-2 ring-blue-100' : 'bg-slate-50/50 border-slate-200/80') }}">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 shadow-xs {{ $stepStatus === 'completed' ? 'bg-emerald-600 text-white' : ($stepStatus === 'in_progress' ? 'bg-[#0D47A1] text-white animate-pulse' : 'bg-slate-200 text-slate-500') }}">
                            @if ($stepStatus === 'completed')
                                <span class="material-symbols-outlined text-[18px]">check</span>
                            @elseif ($stepStatus === 'in_progress')
                                <span class="material-symbols-outlined text-[18px]">sync</span>
                            @else
                                <span>{{ $lw->urutan }}</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-bold {{ $stepStatus === 'completed' ? 'text-emerald-950' : ($stepStatus === 'in_progress' ? 'text-[#0D47A1]' : 'text-slate-700') }} leading-snug">
                                {{ $lw->workflow->nama }}
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                                {{ $lw->workflow->deskripsi ?? 'Tahapan alur' }}
                            </p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $stepStatus === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($stepStatus === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500') }}">
                                    {{ $stepStatus === 'completed' ? 'Selesai' : ($stepStatus === 'in_progress' ? 'Sedang Diproses' : 'Menunggu') }}
                                </span>
                                @if ($wfData && $wfData->completed_at)
                                    <span class="text-[10px] text-slate-400">
                                        {{ $wfData->completed_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Fallback Stepper -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[24px]">task_alt</span>
                    <div>
                        <div class="text-xs font-bold text-emerald-900">1. Usulan Dikirim</div>
                        <div class="text-[11px] text-emerald-700">{{ $pengajuan->created_at->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="p-4 rounded-xl border flex items-center gap-3 {{ in_array($pengajuan->status, ['under_review', 'approved', 'rejected']) ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <span class="material-symbols-outlined text-[24px]">fact_check</span>
                    <div>
                        <div class="text-xs font-bold">2. Telaah APTIKA</div>
                        <div class="text-[11px]">{{ $pengajuan->status === 'under_review' ? 'Dalam Proses' : ($pengajuan->tanggal_penilaian ? $pengajuan->tanggal_penilaian->format('d M Y') : 'Menunggu') }}</div>
                    </div>
                </div>
                <div class="p-4 rounded-xl border flex items-center gap-3 {{ $pengajuan->status === 'approved' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($pengajuan->status === 'rejected' ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    <span class="material-symbols-outlined text-[24px]">verified</span>
                    <div>
                        <div class="text-xs font-bold">3. Keputusan Akhir</div>
                        <div class="text-[11px]">{{ ucfirst($pengajuan->status) }}</div>
                    </div>
                </div>
            </div>
        @endif

        @if ($pengajuan->catatan_penilaian)
            <div class="mt-6 p-4 rounded-xl {{ $pengajuan->status === 'approved' ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
                <div class="flex items-center gap-2 font-bold text-xs {{ $pengajuan->status === 'approved' ? 'text-emerald-900' : 'text-rose-900' }} mb-1">
                    <span class="material-symbols-outlined text-[18px]">{{ $pengajuan->status === 'approved' ? 'task_alt' : 'feedback' }}</span>
                    <span>Catatan Telaah dari Tim Diskominfo Riau:</span>
                </div>
                <p class="text-xs {{ $pengajuan->status === 'approved' ? 'text-emerald-800' : 'text-rose-800' }} leading-relaxed">
                    {{ $pengajuan->catatan_penilaian }}
                </p>
                @if ($pengajuan->penilai)
                    <div class="text-[10px] text-slate-500 mt-2">
                        Ditinjau oleh: <strong>{{ $pengajuan->penilai->name }}</strong> pada {{ $pengajuan->tanggal_penilaian?->format('d F Y, H:i') }} WIB
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Informasi Detail Permohonan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-5">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
                Deskripsi Kebutuhan & Ruang Lingkup Sistem
            </h3>
            <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                {{ $pengajuan->deskripsi_kebutuhan }}
            </p>

            @if ($pengajuan->aplikasi)
                <div class="pt-4 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Aplikasi Terkait</span>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">{{ $pengajuan->aplikasi->nama_aplikasi }}</span>
                            <span class="text-[11px] text-slate-500 font-mono">{{ $pengajuan->aplikasi->url_aplikasi ?? 'URL Internal' }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">Terdaftar</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
                Informasi Pemohon
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Nama Perangkat Daerah</span>
                    <span class="font-bold text-slate-900">{{ $pengajuan->instansi->nama_instansi }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Nama Pejabat / Narahubung</span>
                    <span class="font-semibold text-slate-800">{{ $pengajuan->nama_pemohon }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Nomor Kontak / WhatsApp</span>
                    <span class="font-mono text-slate-800">{{ $pengajuan->no_hp_pemohon }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dokumen Lampiran Berkas & Isian Workflow -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Berkas Lampiran Regulasi Pengajuan</h3>
                <p class="text-xs text-slate-500 mt-0.5">Seluruh berkas terisolasi rapi di direktori storage sistem</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0D47A1] border border-blue-100">
                {{ $pengajuan->dokumen->count() }} Dokumen Terlampir
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($pengajuan->dokumen as $dok)
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">picture_as_pdf</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ $dok->masterDokumen->nama_dokumen ?? 'Dokumen Pendukung' }}
                                </h4>
                                @if ($dok->masterDokumen?->wajib)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Wajib
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">
                                {{ $dok->nama_file_asli }} • <span class="text-slate-400">{{ $dok->formatted_size }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1 font-mono">
                                <span class="material-symbols-outlined text-[13px]">folder</span>
                                <span>{{ $dok->file_path }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0 sm:self-center">
                        <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Buka / Unduh</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-400">
                    <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">folder_off</span>
                    <p class="text-sm font-semibold">Tidak ada berkas dokumen yang tersimpan pada permohonan ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
