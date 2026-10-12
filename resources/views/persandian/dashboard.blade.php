@extends('layouts.persandian')

@section('title', 'Dashboard CSIRT & Keamanan Informasi')
@section('page_title', 'Dashboard Persandian & CSIRT Riau')
@section('page_subtitle', 'Pusat pemantauan audit kerentanan siber, pengujian penetrasi, dan sertifikasi kelaikan keamanan aplikasi SPBE')

@section('content')
<div class="space-y-6">

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Antrean Uji Keamanan -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Antrean Uji Keamanan</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $pendingSecurityTestingCount }}</span>
                    <span class="text-xs font-medium text-amber-600">Menunggu Pentest</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                <span class="material-symbols-outlined text-[26px]">security</span>
            </div>
        </div>

        <!-- Lulus Uji Keamanan -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lulus Audit Siber</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $passedSecurityTestingCount }}</span>
                    <span class="text-xs font-medium text-emerald-600">Terbit SKLO</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <span class="material-symbols-outlined text-[26px]">verified_user</span>
            </div>
        </div>

        <!-- Butuh Perbaikan -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Perlu Mitigasi</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $needRevisionCount }}</span>
                    <span class="text-xs font-medium text-blue-600">Temuan Bug/Celah</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0 border border-blue-100">
                <span class="material-symbols-outlined text-[26px]">build_circle</span>
            </div>
        </div>

        <!-- Celah Tinggi & Kritis -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Celah Kritis / Tinggi</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold {{ $criticalRisksCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $criticalRisksCount }}</span>
                    <span class="text-xs font-medium {{ $criticalRisksCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-500' }}">Tingkat Risiko Tinggi</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $criticalRisksCount > 0 ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-50 text-slate-600 border-slate-100' }} flex items-center justify-center shrink-0 border">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
        </div>
    </div>

    <!-- Security Assurance Banner -->
    <div class="p-5 bg-gradient-to-r from-slate-950 via-slate-900 to-emerald-950 text-white rounded-2xl shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <h3 class="text-base font-bold">Standard Operasional Keamanan Siber (CSIRT Provinsi Riau)</h3>
                </div>
                <p class="text-xs text-slate-300 mt-1">Seluruh aplikasi SPBE wajib lolos uji kerentanan OWASP Top 10 & terbit Surat Kelaikan sebelum dideploy ke server produksi.</p>
            </div>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-xs px-4 py-2.5 rounded-xl border border-white/10 text-xs">
                <span class="material-symbols-outlined text-emerald-400 text-[20px]">policy</span>
                <span>Standar BSSN & ISO 27001</span>
            </div>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Queue & Recent Audits -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Antrean Siap Uji Keamanan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-700">security</span>
                        <h2 class="text-sm font-bold text-slate-900">Antrean Siap Uji Keamanan</h2>
                    </div>
                    <a href="{{ route('persandian.pengujian.index') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                        Lihat Semua ({{ $pendingSecurityTestingCount }})
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>

                @if ($pendingProjects->isEmpty())
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-[24px]">verified</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Tidak Ada Antrean Uji Keamanan</p>
                        <p class="text-xs text-slate-400 mt-1">Saat ini semua proyek telah diaudit atau belum ada yang lulus dari uji fungsional APTIKA.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($pendingProjects as $project)
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $project->nama_project }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Siap Uji Siber
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[14px] text-slate-400">domain</span>
                                        {{ $project->aplikasi?->instansi?->nama_instansi ?? 'Instansi' }} &bull;
                                        <span>Progres: {{ $project->progres_total }}%</span>
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('persandian.pengujian.create', $project) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition">
                                        <span class="material-symbols-outlined text-[16px]">fact_check</span>
                                        <span>Mulai Audit Keamanan</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Log Audit Keamanan Terkini -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-700">history</span>
                        <h2 class="text-sm font-bold text-slate-900">Log Hasil Audit Keamanan Terkini</h2>
                    </div>
                    <a href="{{ route('persandian.pengujian.riwayat') }}" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                        Lihat Seluruh Arsip
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>

                @if ($recentAudits->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat audit keamanan yang tersimpan.
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentAudits as $audit)
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-slate-900">{{ $audit->project?->nama_project ?? '-' }}</span>
                                        @if ($audit->hasil === 'lulus')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Lulus Audit
                                            </span>
                                        @elseif ($audit->hasil === 'perbaikan')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Perlu Perbaikan
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Tidak Lulus
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $audit->project?->aplikasi?->instansi?->nama_instansi ?? '-' }} &bull;
                                        <span class="font-medium text-slate-700">{{ ucwords(str_replace('_', ' ', $audit->kategori_pengujian)) }}</span> &bull;
                                        <span>Tgl: {{ $audit->tanggal_uji?->format('d M Y') }}</span>
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('persandian.pengujian.show', $audit) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                        Lembar Hasil
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Col: Risk Matrix & Quick Links -->
        <div class="space-y-6">

            <!-- Risk Distribution Matrix Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-rose-600">shield</span>
                    <h3 class="text-sm font-bold text-slate-900">Distribusi Risiko Kerentanan</h3>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-50 border border-rose-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                            <span class="font-bold text-rose-900">Kritis (Critical)</span>
                        </div>
                        <span class="font-extrabold text-rose-700">{{ $riskStats['kritis'] }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50 border border-amber-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                            <span class="font-bold text-amber-900">Tinggi (High)</span>
                        </div>
                        <span class="font-extrabold text-amber-700">{{ $riskStats['tinggi'] }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-yellow-50 border border-yellow-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-600"></span>
                            <span class="font-bold text-yellow-900">Sedang (Medium)</span>
                        </div>
                        <span class="font-extrabold text-yellow-700">{{ $riskStats['sedang'] }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <span class="font-bold text-blue-900">Rendah (Low)</span>
                        </div>
                        <span class="font-extrabold text-blue-700">{{ $riskStats['rendah'] }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50 border border-emerald-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span class="font-bold text-emerald-900">Aman (Clean)</span>
                        </div>
                        <span class="font-extrabold text-emerald-700">{{ $riskStats['aman'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-700">bolt</span>
                    Navigasi Cepat
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('persandian.pengujian.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/50 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:scale-105 transition">
                                <span class="material-symbols-outlined text-[18px]">security</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Antrean Uji Keamanan</span>
                                <span class="text-[11px] text-slate-500 block">Proyek menunggu audit siber</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 group-hover:text-emerald-700 text-[18px]">chevron_right</span>
                    </a>

                    <a href="{{ route('persandian.pengujian.riwayat') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0D47A1] flex items-center justify-center group-hover:scale-105 transition">
                                <span class="material-symbols-outlined text-[18px]">folder_shared</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Arsip Seluruh Laporan</span>
                                <span class="text-[11px] text-slate-500 block">Riwayat pentest & rekomendasi</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 group-hover:text-[#0D47A1] text-[18px]">chevron_right</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
