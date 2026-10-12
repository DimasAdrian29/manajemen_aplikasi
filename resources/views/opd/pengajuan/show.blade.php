@extends('layouts.opd')

@section('title', 'Detail Permohonan ' . $pengajuan->kode_pengajuan)
@section('page-title', 'Pelacakan Status Permohonan')
@section('breadcrumb', 'Layanan Aplikasi / ' . $pengajuan->kode_pengajuan)

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="font-mono text-sm font-extrabold text-[#0D47A1] bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                    {{ $pengajuan->kode_pengajuan }}
                </span>
                @if ($pengajuan->jenis_pengajuan === 'new_app')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Aplikasi Baru
                    </span>
                @elseif ($pengajuan->jenis_pengajuan === 'app_development')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        Pengembangan
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        Penonaktifan
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
                <span>Cetak Lembar Tanda Terima</span>
            </button>
        </div>
    </div>

    <!-- Stepper Status Tracking -->
    @php
        $status = $pengajuan->status;
        $isSubmitted = true;
        $isReview = in_array($status, ['under_review', 'approved', 'rejected']);
        $isFinished = in_array($status, ['approved', 'rejected']);
        $isApproved = ($status === 'approved');
        $isRejected = ($status === 'rejected');
    @endphp

    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">Alur Verifikasi & Progres Layanan</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
            <!-- Step 1: Diajukan -->
            <div class="flex md:flex-col items-center md:items-start gap-4 md:gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-emerald-600 text-white shadow-xs">
                    <span class="material-symbols-outlined text-[20px]">check</span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900">1. Pengajuan Dikirim</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Berkas & data tersimpan lengkap</p>
                </div>
            </div>

            <!-- Step 2: Telaah Teknis -->
            <div class="flex md:flex-col items-center md:items-start gap-4 md:gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $isReview ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400' }} shadow-xs">
                    @if ($isFinished)
                        <span class="material-symbols-outlined text-[20px]">check</span>
                    @elseif ($status === 'under_review')
                        <span class="material-symbols-outlined text-[20px] animate-spin">sync</span>
                    @else
                        <span>2</span>
                    @endif
                </div>
                <div>
                    <h4 class="text-xs font-bold {{ $isReview ? 'text-slate-900' : 'text-slate-400' }}">2. Telaah Teknis Kominfo</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Audit kelayakan SPBE & arsitektur</p>
                </div>
            </div>

            <!-- Step 3: Keputusan Kominfo -->
            <div class="flex md:flex-col items-center md:items-start gap-4 md:gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $isFinished ? ($isApproved ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white') : 'bg-slate-100 text-slate-400' }} shadow-xs">
                    @if ($isApproved)
                        <span class="material-symbols-outlined text-[20px]">verified</span>
                    @elseif ($isRejected)
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    @else
                        <span>3</span>
                    @endif
                </div>
                <div>
                    <h4 class="text-xs font-bold {{ $isFinished ? ($isApproved ? 'text-emerald-700' : 'text-rose-700') : 'text-slate-400' }}">
                        3. Keputusan Telaah
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        {{ $isApproved ? 'Disetujui untuk dikerjakan' : ($isRejected ? 'Ditolak / Perlu Revisi' : 'Menunggu hasil verifikasi') }}
                    </p>
                </div>
            </div>

            <!-- Step 4: Proyek Pengerjaan -->
            <div class="flex md:flex-col items-center md:items-start gap-4 md:gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $pengajuan->project ? 'bg-[#0D47A1] text-white' : 'bg-slate-100 text-slate-400' }} shadow-xs">
                    <span class="material-symbols-outlined text-[20px]">code</span>
                </div>
                <div>
                    <h4 class="text-xs font-bold {{ $pengajuan->project ? 'text-[#0D47A1]' : 'text-slate-400' }}">4. Proyek Pengembangan</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        {{ $pengajuan->project ? 'Proyek sedang dikerjakan' : 'Masuk antrean sprint tim' }}
                    </p>
                </div>
            </div>
        </div>

        @if ($pengajuan->catatan_penilaian)
            <div class="mt-6 p-4 rounded-xl {{ $isApproved ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
                <div class="flex items-center gap-2 font-bold text-xs {{ $isApproved ? 'text-emerald-900' : 'text-rose-900' }} mb-1">
                    <span class="material-symbols-outlined text-[18px]">{{ $isApproved ? 'task_alt' : 'feedback' }}</span>
                    <span>Catatan Telaah dari Tim Diskominfo Riau:</span>
                </div>
                <p class="text-xs {{ $isApproved ? 'text-emerald-800' : 'text-rose-800' }} leading-relaxed">
                    {{ $pengajuan->catatan_penilaian }}
                </p>
                @if ($pengajuan->penilai)
                    <div class="text-[10px] text-slate-500 mt-2">
                        Diverifikasi oleh: <strong>{{ $pengajuan->penilai->name }}</strong> ({{ $pengajuan->tanggal_penilaian?->format('d M Y, H:i') }})
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Informasi Detail & Narahubung -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Rincian Deskripsi & Ruang Lingkup</h3>
            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-xl border border-slate-200/60">
                {{ $pengajuan->deskripsi_kebutuhan }}
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Data Pemohon</h3>
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

    <!-- Dokumen Lampiran Berkas (Terisolasi per jenis) -->
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
