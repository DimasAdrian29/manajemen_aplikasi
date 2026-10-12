@extends('layouts.aptika')

@section('title', 'Proyek ' . $project->nama_project)
@section('page-title', 'Detail Manajemen Proyek')
@section('breadcrumb', 'Manajemen Proyek / #PRJ-' . str_pad($project->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    <!-- Header Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="font-mono text-xs font-bold bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded border border-slate-200">
                    #PRJ-{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                </span>

                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                    @if ($project->status === 'initiated') bg-slate-100 text-slate-700
                    @elseif ($project->status === 'in_development') bg-purple-50 text-purple-700 border border-purple-200
                    @elseif ($project->status === 'functional_testing') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif ($project->status === 'security_testing') bg-amber-50 text-amber-700 border border-amber-200
                    @elseif ($project->status === 'deployment') bg-blue-50 text-blue-700 border border-blue-200
                    @elseif ($project->status === 'live') bg-teal-50 text-teal-700 border border-teal-200
                    @else bg-rose-50 text-rose-700 border border-rose-200
                    @endif">
                    Status: {{ str_replace('_', ' ', $project->status) }}
                </span>
            </div>

            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                {{ $project->nama_project }}
            </h2>
            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                <span>Instansi: <strong>{{ $project->aplikasi->instansi->nama_instansi ?? '-' }}</strong></span>
                <span>•</span>
                <span>Siklus: <strong>{{ $project->tanggal_mulai?->format('d M Y') }} s/d {{ $project->tanggal_selesai?->format('d M Y') }}</strong></span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <!-- Modal / Form Ubah Status Cepat -->
            <form method="POST" action="{{ route('aptika.project.status', $project) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer">
                    <option value="initiated" {{ $project->status === 'initiated' ? 'selected' : '' }}>Inisiasi Tim</option>
                    <option value="in_development" {{ $project->status === 'in_development' ? 'selected' : '' }}>Dalam Pengembangan</option>
                    <option value="functional_testing" {{ $project->status === 'functional_testing' ? 'selected' : '' }}>Uji Fungsional (APTIKA)</option>
                    <option value="security_testing" {{ $project->status === 'security_testing' ? 'selected' : '' }}>Uji Keamanan (Persandian)</option>
                    <option value="deployment" {{ $project->status === 'deployment' ? 'selected' : '' }}>Hosting & Deployment</option>
                    <option value="live" {{ $project->status === 'live' ? 'selected' : '' }}>Live Beroperasi</option>
                    <option value="delayed" {{ $project->status === 'delayed' ? 'selected' : '' }}>Ditunda (Pending)</option>
                </select>
                <button type="submit" class="px-3 py-2 bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold rounded-xl transition cursor-pointer">
                    Update Status
                </button>
            </form>

            <a href="{{ route('aptika.rapat.create', ['project_id' => $project->id]) }}" class="px-3.5 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">groups</span>
                <span>Rapat</span>
            </a>

            <a href="{{ route('aptika.testing.create', ['project_id' => $project->id]) }}" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">fact_check</span>
                <span>Uji Fungsional</span>
            </a>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Capaian Progres Pengembangan Keseluruhan</span>
            <span class="text-lg font-extrabold text-[#0D47A1]">{{ $project->progres_total }}%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
            <div class="bg-gradient-to-r from-[#0D47A1] to-[#1E88E5] h-3 rounded-full transition-all duration-500" style="width: {{ $project->progres_total }}%"></div>
        </div>
    </div>

    @if ($project->hostingAplikasi)
        <!-- Section Hosting & Server Info (Bidang Infrastruktur) -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center shrink-0 border border-cyan-100">
                        <span class="material-symbols-outlined text-[22px]">dns</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Alokasi Server & Hosting Produksi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Dikelola oleh Bidang Infrastruktur & Jaringan (TIK)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ strtoupper($project->hostingAplikasi->status_hosting) }}
                    </span>
                    @if ($project->hostingAplikasi->url_aplikasi)
                        <a href="{{ $project->hostingAplikasi->url_aplikasi }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] transition">
                            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            Buka URL
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-400 block text-[11px]">Domain Publik</span>
                    <span class="font-bold text-slate-800 font-mono">{{ $project->hostingAplikasi->nama_domain ?? '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-400 block text-[11px]">IP Server</span>
                    <span class="font-bold text-slate-800 font-mono">{{ $project->hostingAplikasi->ip_server ?? '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-400 block text-[11px]">Jenis Server</span>
                    <span class="font-bold text-slate-800 uppercase">{{ $project->hostingAplikasi->jenis_hosting ?? 'VPS' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-400 block text-[11px]">Sertifikat SSL</span>
                    <span class="font-bold {{ $project->hostingAplikasi->ssl_aktif ? 'text-emerald-700' : 'text-slate-500' }}">
                        {{ $project->hostingAplikasi->ssl_aktif ? 'Terenkripsi (HTTPS)' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- Section 1: Tim Pengembang Internal -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-purple-600">group</span>
                    <span>Susunan Tim Pengembang Proyek</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Personil teknis internal yang ditugaskan mengerjakan aplikasi</p>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                    {{ $project->tim->count() }} Personil
                </span>
                <button 
                    type="button" 
                    onclick="openModalKelolaTim()" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition shadow-xs cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[16px]">tune</span>
                    <span>Kelola Tim Proyek</span>
                </button>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($project->tim as $member)
                <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50/50 transition">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-full {{ $member->peran === 'ketua_tim' ? 'bg-[#0D47A1]' : 'bg-slate-200' }} text-white flex items-center justify-center font-bold text-sm shrink-0">
                            {{ substr($member->user->name ?? 'U', 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">{{ $member->user->name }}</h4>
                                @if ($member->peran === 'ketua_tim')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-[#0D47A1] text-white uppercase tracking-wider">
                                        Ketua Tim
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        Anggota Tim
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $member->user->jabatan ?? 'Tenaga Pengembang' }} • <span class="font-mono text-slate-400">{{ $member->user->email }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($member->user->no_hp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member->user->no_hp) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold flex items-center gap-1 transition">
                            <span class="material-symbols-outlined text-[15px]">chat</span>
                            <span>{{ $member->user->no_hp }}</span>
                        </a>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada tim yang ditugaskan pada proyek ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section: Dokumen Perancangan Teknis Sistem & Tautan Cloud Docs -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">description</span>
                    <span>Dokumen Perancangan Sistem & Tautan Cloud Docs</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Dokumen SRS, ERD, arsitektur, dan tautan kerja (Google Docs/Figma) yang diajukan oleh Ketua Tim</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0D47A1] border border-blue-200">
                    {{ $project->dokumenPerancangan->count() }} Dokumen
                </span>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($project->dokumenPerancangan as $doc)
                @php
                    $isExternalLink = str_starts_with($doc->file_dokumen, 'http://') || str_starts_with($doc->file_dokumen, 'https://');
                @endphp
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl {{ $isExternalLink ? 'bg-blue-50 text-blue-600 border border-blue-200/60' : 'bg-rose-50 text-rose-600 border border-rose-200/60' }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">
                                {{ $isExternalLink ? 'link' : 'picture_as_pdf' }}
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-tight">
                                    Dokumen {{ $doc->jenis_dokumen }}
                                </h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    @if ($doc->status === 'disetujui') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif ($doc->status === 'ditolak') bg-rose-50 text-rose-700 border border-rose-200
                                    @else bg-amber-50 text-amber-700 border border-amber-200
                                    @endif">
                                    {{ str_replace('_', ' ', $doc->status) }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                @if ($isExternalLink)
                                    <span class="text-blue-700 font-medium">Tautan Cloud Online:</span>
                                    <a href="{{ $doc->file_dokumen }}" target="_blank" class="font-mono text-slate-500 hover:text-[#0D47A1] underline truncate max-w-xs sm:max-w-md inline-block align-bottom">{{ $doc->file_dokumen }}</a>
                                @else
                                    <span class="text-slate-400">Berkas Tersimpan:</span>
                                    <span class="font-mono text-slate-600">{{ basename($doc->file_dokumen) }}</span>
                                @endif
                                <span class="text-slate-300 mx-1.5">•</span>
                                <span class="text-slate-400">Diunggah: {{ $doc->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                            @if ($doc->catatan)
                                <div class="mt-2 text-xs p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-600">
                                    <strong>Catatan Evaluasi APTIKA:</strong> {{ $doc->catatan }}
                                    @if ($doc->penilai)
                                        <span class="text-[11px] text-slate-400 block mt-0.5">Oleh {{ $doc->penilai->name }} pada {{ $doc->tanggal_penilaian?->format('d M Y, H:i') }} WIB</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 shrink-0 sm:self-center">
                        @if ($isExternalLink)
                            <a href="{{ $doc->file_dokumen }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#0D47A1] text-xs font-bold border border-blue-200/80 transition" title="Buka tautan Google Docs / Figma">
                                <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                <span>Buka Link</span>
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition" title="Unduh berkas perancangan">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                                <span>Unduh</span>
                            </a>
                        @endif

                        <button 
                            type="button" 
                            onclick="openModalReviewDokumen({{ $doc->id }}, '{{ strtoupper($doc->jenis_dokumen) }}', '{{ $doc->status }}', '{{ addslashes($doc->catatan ?? '') }}')" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold transition shadow-2xs cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[16px]">rate_review</span>
                            <span>Telaah Dokumen</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Ketua Tim pengembang belum mengunggah dokumen perancangan (SRS, ERD, dsb.) atau menautkan link Google Docs pada proyek ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Hasil Uji Fungsional Aplikasi -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">fact_check</span>
                    <span>Audit & Uji Fungsional Sistem (APTIKA)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pengujian fitur dan kesesuaian alur sebelum diserahkan ke Persandian</p>
            </div>
            <a href="{{ route('aptika.testing.create', ['project_id' => $project->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition">
                <span class="material-symbols-outlined text-[15px]">add</span>
                <span>Input Hasil Uji Baru</span>
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($project->testingAplikasi as $test)
                <div class="p-5 hover:bg-slate-50/50 transition space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                                @if ($test->status_validasi === 'lulus') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif ($test->status_validasi === 'perbaikan') bg-amber-50 text-amber-700 border border-amber-200
                                @else bg-rose-50 text-rose-700 border border-rose-200
                                @endif">
                                {{ $test->status_validasi }}
                            </span>
                            <span class="text-xs text-slate-400">
                                Diuji pada: {{ $test->tanggal_testing?->format('d F Y') }} oleh <strong>{{ $test->penguji->name ?? 'Admin APTIKA' }}</strong>
                            </span>
                        </div>

                        @if ($test->file_hasil)
                            <a href="{{ asset('storage/' . $test->file_hasil) }}" target="_blank" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">description</span>
                                <span>Unduh Laporan Uji</span>
                            </a>
                        @endif
                    </div>

                    @if ($test->catatan_validasi)
                        <div class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <strong>Catatan Audit:</strong> {{ $test->catatan_validasi }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada catatan uji fungsional untuk proyek ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 3: Hasil Audit Keamanan Informasi (CSIRT / Persandian) -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-blue-600">verified_user</span>
                    <span>Audit & Sertifikasi Keamanan Informasi (CSIRT Riau)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pengujian penetrasi, kerentanan (vulnerability assessment), dan surat kelaikan operasi (SKLO)</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                {{ $project->pengujianKeamanan->count() }} Kali Diuji
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($project->pengujianKeamanan as $audit)
                <div class="p-5 hover:bg-slate-50/50 transition space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                                @if ($audit->hasil === 'lulus') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif ($audit->hasil === 'perbaikan') bg-amber-50 text-amber-700 border border-amber-200
                                @else bg-rose-50 text-rose-700 border border-rose-200
                                @endif">
                                {{ $audit->hasil }}
                            </span>
                            @if ($audit->tingkat_risiko)
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase
                                    @if ($audit->tingkat_risiko === 'kritis') bg-rose-100 text-rose-800
                                    @elseif ($audit->tingkat_risiko === 'tinggi') bg-amber-100 text-amber-800
                                    @elseif ($audit->tingkat_risiko === 'sedang') bg-yellow-100 text-yellow-800
                                    @else bg-emerald-100 text-emerald-800
                                    @endif">
                                    Risiko: {{ $audit->tingkat_risiko }}
                                </span>
                            @endif
                            <span class="text-xs text-slate-400">
                                Diuji: {{ $audit->tanggal_uji?->format('d F Y') }} oleh <strong>{{ $audit->tester->name ?? 'Auditor CSIRT' }}</strong>
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($audit->nomor_surat)
                                <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                    SKLO: {{ $audit->nomor_surat }}
                                </span>
                            @endif
                            @if ($audit->file_hasil)
                                <a href="{{ asset('storage/' . $audit->file_hasil) }}" target="_blank" class="text-xs font-semibold text-[#0D47A1] hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">description</span>
                                    <span>Laporan Audit</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    @if ($audit->catatan_kerentanan)
                        <div class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <strong>Temuan Kerentanan:</strong> {{ $audit->catatan_kerentanan }}
                        </div>
                    @endif
                    @if ($audit->rekomendasi)
                        <div class="text-xs text-emerald-800 bg-emerald-50/60 p-3 rounded-xl border border-emerald-100">
                            <strong>Rekomendasi Mitigasi:</strong> {{ $audit->rekomendasi }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada catatan audit keamanan informasi untuk proyek ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 4: Rapat Koordinasi Terkait Proyek Ini -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-rose-600">groups</span>
                    <span>Rapat Koordinasi & Notulensi Proyek</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pertemuan koordinasi tim pengembang dengan OPD pemohon</p>
            </div>
            <a href="{{ route('aptika.rapat.create', ['project_id' => $project->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition">
                <span class="material-symbols-outlined text-[15px]">add</span>
                <span>Jadwalkan Rapat</span>
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($project->rapat as $rapat)
                <div class="p-5 hover:bg-slate-50/50 transition flex items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-900">{{ $rapat->nama_rapat }}</h4>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                @if ($rapat->status === 'selesai') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif ($rapat->status === 'dibatalkan') bg-rose-50 text-rose-700 border border-rose-200
                                @else bg-blue-50 text-blue-700 border border-blue-200
                                @endif">
                                {{ $rapat->status }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            Waktu: <strong>{{ $rapat->waktu_rapat->format('d M Y, H:i') }} WIB</strong> • Tempat: {{ $rapat->tempat_rapat }}
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            {{ $rapat->peserta->count() }} Peserta terhubung
                        </div>
                    </div>

                    <a href="{{ route('aptika.rapat.show', $rapat) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1">
                        <span>Buka Lembar Rapat</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada agenda rapat yang dijadwalkan pada proyek ini.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Kelola Tim Proyek -->
@php
    $currentKetuaId = $project->tim->where('peran', 'ketua_tim')->first()?->user_id;
    $currentAnggotaIds = $project->tim->where('peran', 'anggota')->pluck('user_id')->toArray();
@endphp

<div id="modalKelolaTim" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" onclick="closeModalKelolaTim()"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-xl rounded-2xl bg-white border border-slate-200/80 shadow-2xl p-6 sm:p-7 z-10">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-purple-600">group_add</span>
                        <span>Kelola Susunan Tim Proyek</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan Ketua Tim dan anggota yang ditugaskan pada proyek ini.</p>
                </div>
                <button type="button" onclick="closeModalKelolaTim()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" action="{{ route('aptika.project.tim', $project) }}" class="space-y-5 pt-4">
                @csrf
                @method('PATCH')

                <!-- 1. Ketua Tim -->
                <div>
                    <label for="modal_ketua_tim_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Ketua Tim Pengembang (Lead) <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="ketua_tim_id" 
                        id="modal_ketua_tim_id" 
                        onchange="syncModalKetuaAndAnggota()"
                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                        <option value="">-- Pilih Ketua Tim --</option>
                        @foreach ($developers as $dev)
                            <option value="{{ $dev->id }}" {{ $currentKetuaId == $dev->id ? 'selected' : '' }}>
                                {{ $dev->name }} &mdash; {{ $dev->jabatan ?? 'Tenaga Pengembang' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Anggota Tim -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Anggota Tim Pengembang
                        </label>
                        <span class="text-[11px] text-slate-400">Pilih satu atau lebih programmer pendukung</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-56 overflow-y-auto p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                        @forelse ($developers as $dev)
                            <label id="modal-member-label-{{ $dev->id }}" class="flex items-center gap-2.5 p-2 rounded-lg bg-white border border-slate-200/80 hover:border-slate-300 cursor-pointer transition select-none">
                                <input 
                                    type="checkbox" 
                                    name="anggota_tim_ids[]" 
                                    value="{{ $dev->id }}" 
                                    id="modal-member-checkbox-{{ $dev->id }}"
                                    {{ in_array($dev->id, $currentAnggotaIds) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1] border-slate-300"
                                >
                                <div class="text-xs overflow-hidden flex-1">
                                    <div class="font-bold text-slate-900 truncate">{{ $dev->name }}</div>
                                    <div class="text-[11px] text-slate-500 truncate">{{ $dev->jabatan ?? 'Programmer' }}</div>
                                </div>
                                <span id="modal-badge-lead-{{ $dev->id }}" class="hidden text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 shrink-0">
                                    Ketua
                                </span>
                            </label>
                        @empty
                            <div class="col-span-full text-center py-4 text-xs text-slate-400">
                                Belum ada personil tim pengembang terdaftar.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModalKelolaTim()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm shadow-purple-600/20 transition flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                        <span>Simpan Perubahan Tim</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Telaah Dokumen Perancangan -->
<div id="modalReviewDokumen" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" onclick="closeModalReviewDokumen()"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-2xl bg-white border border-slate-200/80 shadow-2xl p-6 sm:p-7 z-10">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">rate_review</span>
                        <span>Telaah <span id="modalReviewDocName">Dokumen</span></span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Berikan persetujuan atau catatan evaluasi untuk dokumen perancangan tim.</p>
                </div>
                <button type="button" onclick="closeModalReviewDokumen()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="formReviewDokumen" method="POST" action="" class="space-y-4 pt-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Telaah Dokumen <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white hover:bg-emerald-50/50 cursor-pointer transition text-center select-none has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/40 has-[:checked]:text-emerald-800">
                            <input type="radio" name="status" value="disetujui" class="sr-only" required>
                            <span class="material-symbols-outlined text-[22px] text-emerald-600 mb-1">check_circle</span>
                            <span class="text-xs font-bold">Disetujui</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white hover:bg-rose-50/50 cursor-pointer transition text-center select-none has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/40 has-[:checked]:text-rose-800">
                            <input type="radio" name="status" value="ditolak" class="sr-only" required>
                            <span class="material-symbols-outlined text-[22px] text-rose-600 mb-1">cancel</span>
                            <span class="text-xs font-bold">Perlu Revisi</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white hover:bg-amber-50/50 cursor-pointer transition text-center select-none has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/40 has-[:checked]:text-amber-800">
                            <input type="radio" name="status" value="menunggu_review" class="sr-only" required>
                            <span class="material-symbols-outlined text-[22px] text-amber-600 mb-1">hourglass_top</span>
                            <span class="text-xs font-bold">Dalam Kajian</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="review_catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Evaluasi / Arahan Revisi
                    </label>
                    <textarea 
                        name="catatan" 
                        id="review_catatan" 
                        rows="3" 
                        placeholder="Berikan masukan teknis atau alasan jika memerlukan perbaikan (opsional)..." 
                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    ></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModalReviewDokumen()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                        <span>Simpan Hasil Telaah</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModalKelolaTim() {
        document.getElementById('modalKelolaTim').classList.remove('hidden');
        syncModalKetuaAndAnggota();
    }

    function closeModalKelolaTim() {
        document.getElementById('modalKelolaTim').classList.add('hidden');
    }

    function syncModalKetuaAndAnggota() {
        const ketuaId = document.getElementById('modal_ketua_tim_id').value;
        const checkboxes = document.querySelectorAll('input[name="anggota_tim_ids[]"]');

        checkboxes.forEach(cb => {
            const label = document.getElementById('modal-member-label-' + cb.value);
            const badge = document.getElementById('modal-badge-lead-' + cb.value);
            if (cb.value === ketuaId) {
                cb.checked = false;
                cb.disabled = true;
                if (label) {
                    label.classList.add('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
                    label.classList.remove('cursor-pointer');
                }
                if (badge) badge.classList.remove('hidden');
            } else {
                cb.disabled = false;
                if (label) {
                    label.classList.remove('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
                    label.classList.add('cursor-pointer');
                }
                if (badge) badge.classList.add('hidden');
            }
        });
    }

    function openModalReviewDokumen(docId, docName, currentStatus, currentCatatan) {
        const form = document.getElementById('formReviewDokumen');
        form.action = "{{ url('aptika/project/' . $project->id . '/dokumen') }}/" + docId + "/review";
        document.getElementById('modalReviewDocName').innerText = 'Dokumen ' + docName;
        document.getElementById('review_catatan').value = currentCatatan || '';
        
        const radios = form.querySelectorAll('input[name="status"]');
        radios.forEach(r => {
            r.checked = (r.value === currentStatus);
        });

        document.getElementById('modalReviewDokumen').classList.remove('hidden');
    }

    function closeModalReviewDokumen() {
        document.getElementById('modalReviewDokumen').classList.add('hidden');
    }
</script>
@endsection
