@extends('layouts.persandian')

@section('title', 'Lembar Hasil Audit Keamanan - ' . ($pengujian->project?->nama_project ?? 'Pengujian'))
@section('page_title', 'Lembar Hasil Audit Keamanan Informasi')
@section('page_subtitle', 'Dokumentasi resmi evaluasi kerentanan siber, analisis risiko, dan rekomendasi mitigasi CSIRT Riau')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('persandian.pengujian.riwayat') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Arsip Riwayat Audit</span>
        </a>
        <div class="flex items-center gap-2">
            @if ($pengujian->file_hasil)
                <a href="{{ asset('storage/' . $pengujian->file_hasil) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#0D47A1] bg-blue-50 border border-blue-200 hover:bg-blue-100 transition shadow-xs">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Unduh Berkas Laporan Audit</span>
                </a>
            @endif
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Lembar</span>
            </button>
        </div>
    </div>

    <!-- Header Certificate Card -->
    <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl {{ $pengujian->hasil === 'lulus' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($pengujian->hasil === 'perbaikan' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100') }} flex items-center justify-center shrink-0 border">
                    <span class="material-symbols-outlined text-[32px]">
                        {{ $pengujian->hasil === 'lulus' ? 'verified_user' : ($pengujian->hasil === 'perbaikan' ? 'build_circle' : 'gpp_bad') }}
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-bold text-slate-900">{{ $pengujian->project?->nama_project ?? '-' }}</h2>
                        @if ($pengujian->hasil === 'lulus')
                            <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Lulus (Laik Operasi)
                            </span>
                        @elseif ($pengujian->hasil === 'perbaikan')
                            <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                Perlu Perbaikan
                            </span>
                        @else
                            <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                Tidak Lulus
                            </span>
                        @endif

                        <!-- Tingkat Risiko Badge -->
                        @php
                            $riskColor = match($pengujian->tingkat_risiko) {
                                'kritis' => 'bg-rose-100 text-rose-900 border-rose-300',
                                'tinggi' => 'bg-amber-100 text-amber-900 border-amber-300',
                                'sedang' => 'bg-yellow-100 text-yellow-900 border-yellow-300',
                                'rendah' => 'bg-blue-100 text-blue-900 border-blue-300',
                                default => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border {{ $riskColor }} uppercase">
                            Risiko: {{ $pengujian->tingkat_risiko }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 mt-1">
                        Aplikasi: <strong class="text-slate-800">{{ $pengujian->project?->aplikasi?->nama_aplikasi ?? '-' }}</strong> &bull;
                        Instansi: <span class="text-slate-700">{{ $pengujian->project?->aplikasi?->instansi?->nama_instansi ?? 'Pemerintah Provinsi Riau' }}</span>
                    </p>

                    @if ($pengujian->nomor_surat)
                        <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-50 border border-slate-200 text-xs font-mono font-semibold text-slate-800">
                            <span class="material-symbols-outlined text-[16px] text-slate-500">tag</span>
                            <span>SKLO: {{ $pengujian->nomor_surat }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="shrink-0 flex md:flex-col items-end gap-1 text-xs">
                <span class="text-slate-400 text-[11px]">Tanggal Pengujian</span>
                <span class="font-bold text-slate-800">{{ $pengujian->tanggal_uji?->format('d F Y') ?? '-' }}</span>
                <span class="text-slate-400 text-[11px] mt-1">Auditor CSIRT</span>
                <span class="font-medium text-slate-700">{{ $pengujian->tester?->name ?? 'Bidang Persandian' }}</span>
            </div>
        </div>
    </div>

    <!-- Parameter & Metadata Grid -->
    <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
            <span class="material-symbols-outlined text-emerald-700">info</span>
            <h3 class="text-sm font-bold text-slate-900">Rincian Parameter Pengujian</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Metode / Kategori Uji</span>
                <span class="font-bold text-slate-800 mt-0.5 block">
                    {{ ucwords(str_replace('_', ' ', $pengujian->kategori_pengujian)) }}
                </span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Status Siklus Proyek Saat Ini</span>
                <span class="font-bold text-[#0D47A1] mt-0.5 block uppercase">
                    {{ str_replace('_', ' ', $pengujian->project?->status ?? '-') }}
                </span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Ketua Tim Pengembang</span>
                <span class="font-bold text-slate-800 mt-0.5 block">
                    {{ $pengujian->project?->tim?->where('peran', 'ketua_tim')->first()?->user?->name ?? 'Belum Ditentukan' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Temuan Kerentanan & Saran Mitigasi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Catatan Temuan Celah -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-rose-700 font-bold text-sm">
                <span class="material-symbols-outlined text-[20px]">bug_report</span>
                <h3>Daftar Temuan Kerentanan Siber</h3>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-800 whitespace-pre-line leading-relaxed min-h-[120px]">
                {{ $pengujian->catatan_kerentanan ?: 'Tidak ada catatan kerentanan khusus yang dilaporkan.' }}
            </div>
        </div>

        <!-- Rekomendasi Tindakan Mitigasi -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-emerald-800 font-bold text-sm">
                <span class="material-symbols-outlined text-[20px]">recommend</span>
                <h3>Rekomendasi Tindakan Mitigasi</h3>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 whitespace-pre-line leading-relaxed min-h-[120px]">
                {{ $pengujian->rekomendasi ?: 'Sistem telah memenuhi standar keamanan yang dipersyaratkan.' }}
            </div>
        </div>

    </div>

    <!-- Keterangan Umum & Catatan Auditor -->
    @if ($pengujian->keterangan)
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Tambahan Auditor</h4>
            <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                {{ $pengujian->keterangan }}
            </p>
        </div>
    @endif

</div>
@endsection
