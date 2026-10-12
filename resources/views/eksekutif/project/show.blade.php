@extends('layouts.eksekutif')

@section('title', 'Detail Proyek - ' . $project->nama_project)
@section('page-title', 'Detail Lembar Kerja Eksekutif Proyek')
@section('breadcrumb', 'Portofolio Proyek / Detail')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('eksekutif.project.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Portofolio Proyek</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                @if ($project->status === 'live') bg-emerald-50 text-emerald-700 border border-emerald-200
                @elseif ($project->status === 'delayed') bg-rose-50 text-rose-700 border border-rose-200
                @elseif ($project->status === 'deployment') bg-cyan-50 text-cyan-700 border border-cyan-200
                @elseif ($project->status === 'security_testing') bg-purple-50 text-purple-700 border border-purple-200
                @elseif ($project->status === 'functional_testing') bg-teal-50 text-teal-700 border border-teal-200
                @else bg-blue-50 text-blue-700 border border-blue-200
                @endif">
                Status: {{ str_replace('_', ' ', $project->status) }}
            </span>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-2 max-w-2xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">
                    {{ $project->aplikasi->instansi->nama_instansi ?? 'Pemerintah Provinsi Riau' }}
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $project->nama_project }}</h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ $project->aplikasi->deskripsi ?? 'Proyek sistem aplikasi pendukung layanan daerah Pemerintah Provinsi Riau.' }}
                </p>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 min-w-[200px] shrink-0 space-y-2">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Capaian Progres Total</div>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-black text-slate-900">{{ $project->progres_total }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-300 {{ $project->progres_total >= 100 ? 'bg-emerald-500' : ($project->progres_total > 50 ? 'bg-[#0D47A1]' : 'bg-amber-500') }}" style="width: {{ $project->progres_total }}%"></div>
                </div>
            </div>
        </div>

        <!-- 3 Key Metric Badges -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="text-slate-400 block text-[11px]">Tanggal Mulai Pengerjaan</span>
                <span class="font-bold text-slate-800 mt-0.5 block font-mono">
                    {{ $project->tanggal_mulai ? $project->tanggal_mulai->format('d M Y') : '-' }}
                </span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="text-slate-400 block text-[11px]">Target Selesai Rilis</span>
                <span class="font-bold text-slate-800 mt-0.5 block font-mono">
                    {{ $project->tanggal_selesai ? $project->tanggal_selesai->format('d M Y') : 'Belum Ditentukan' }}
                </span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="text-slate-400 block text-[11px]">Kategori Aplikasi</span>
                <span class="font-bold text-slate-800 mt-0.5 block">
                    {{ ucfirst(str_replace('_', ' ', $project->aplikasi->status ?? 'aktif')) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Audit Mutu, Keamanan & Deployment (Ringkasan Eksekutif) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- 1. Uji Fungsional APTIKA -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-5 space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-teal-600">checklist</span>
                    <span>Uji Fungsional (APTIKA)</span>
                </h4>
            </div>
            @php
                $testFungsional = $project->testingAplikasi->last();
            @endphp
            @if ($testFungsional)
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Hasil Evaluasi:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $testFungsional->status_testing === 'lulus' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $testFungsional->status_testing }}
                        </span>
                    </div>
                    <div class="text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100 leading-relaxed">
                        {{ $testFungsional->catatan_testing ?? 'Tidak ada catatan.' }}
                    </div>
                </div>
            @else
                <div class="py-4 text-center text-xs text-slate-400 italic">
                    Belum dilakukan uji fungsional teknis.
                </div>
            @endif
        </div>

        <!-- 2. Uji Keamanan CSIRT (Persandian) -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-5 space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-purple-600">security</span>
                    <span>Audit Siber (CSIRT)</span>
                </h4>
            </div>
            @php
                $testKeamanan = $project->pengujianKeamanan->last();
            @endphp
            @if ($testKeamanan)
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Hasil Audit:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $testKeamanan->hasil === 'lulus' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ ucfirst($testKeamanan->hasil) }}
                        </span>
                    </div>
                    @if ($testKeamanan->tingkat_risiko)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Tingkat Risiko:</span>
                            <span class="font-bold uppercase text-[10px] text-slate-800">{{ $testKeamanan->tingkat_risiko }}</span>
                        </div>
                    @endif
                    <div class="text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100 leading-relaxed">
                        {{ $testKeamanan->rekomendasi ?? ($testKeamanan->keterangan ?? 'Audit penetrasi selesai dilakukan.') }}
                    </div>
                </div>
            @else
                <div class="py-4 text-center text-xs text-slate-400 italic">
                    Belum melalui audit penetrasi siber CSIRT.
                </div>
            @endif
        </div>

        <!-- 3. Infrastruktur & Hosting -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-5 space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-blue-600">dns</span>
                    <span>Hosting & Infrastruktur</span>
                </h4>
            </div>
            @php
                $hosting = $project->hostingAplikasi;
            @endphp
            @if ($hosting)
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Status Server:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700">
                            {{ $hosting->status_hosting ?? 'aktif' }}
                        </span>
                    </div>
                    <div class="text-slate-700 font-mono text-[11px] bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                        {{ $hosting->nama_domain ?? ($hosting->ip_server ?? 'Server Pemprov Riau') }}
                    </div>
                </div>
            @else
                <div class="py-4 text-center text-xs text-slate-400 italic">
                    Belum ada alokasi server & hosting resmi.
                </div>
            @endif
        </div>

    </div>

    <!-- Struktur Personil Tim Pengembang -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
        <h3 class="text-sm font-bold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">groups</span>
            <span>Susunan Personil Tim Teknis Pengembang</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-5">
            @forelse ($project->tim as $personil)
                <div class="p-4 rounded-xl border border-slate-200/70 bg-slate-50/50 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $personil->peran === 'ketua_tim' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-700' }} flex items-center justify-center font-bold text-xs uppercase shrink-0">
                        {{ substr($personil->user->name ?? 'U', 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-900 truncate">{{ $personil->user->name ?? '-' }}</div>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $personil->peran === 'ketua_tim' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ $personil->peran === 'ketua_tim' ? 'Ketua Tim (Lead)' : 'Anggota Tim' }}
                        </span>
                        <div class="text-[11px] text-slate-500 mt-1">{{ $personil->tugas ?? ($personil->user->jabatan ?? '-') }}</div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-xs text-slate-400 py-6">
                    Belum ada personil tim yang ditugaskan pada proyek ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Dokumen Perancangan & Tautan Google Docs -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
        <h3 class="text-sm font-bold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">description</span>
            <span>Dokumen Spesifikasi & Perancangan Sistem</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
            @forelse ($project->dokumenPerancangan as $doc)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                    <div class="min-w-0 pr-3">
                        <span class="text-[10px] font-bold text-[#0D47A1] uppercase tracking-wider block">
                            {{ strtoupper($doc->jenis_dokumen) }}
                        </span>
                        <h4 class="text-xs font-bold text-slate-900 truncate mt-0.5">
                            {{ $doc->nama_dokumen ?? 'Dokumen Spesifikasi ' . strtoupper($doc->jenis_dokumen) }}
                        </h4>
                        @if ($doc->catatan)
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $doc->catatan }}</p>
                        @endif
                    </div>
                    @if (str_starts_with($doc->file_dokumen, 'http://') || str_starts_with($doc->file_dokumen, 'https://'))
                        <a href="{{ $doc->file_dokumen }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition shrink-0 flex items-center gap-1 border border-blue-200">
                            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            <span>Buka Link Docs</span>
                        </a>
                    @elseif ($doc->file_dokumen)
                        <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition shrink-0 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">download</span>
                            <span>Unduh Berkas</span>
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-2 text-center text-xs text-slate-400 py-6">
                    Belum ada dokumen perancangan yang diunggah.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
