@extends('layouts.tim')

@section('title', $project->nama_project)
@section('page-title', 'Detail Proyek Pengembangan')
@section('breadcrumb', 'Proyek / ' . $project->nama_project)

@section('content')
<div class="space-y-6">

    <!-- Back Button & Status Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('tim.project.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Daftar Proyek</span>
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

    <!-- Role Indicator Banner -->
    @if ($isLead)
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-200/90 text-amber-900 flex items-start gap-3.5 shadow-2xs">
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[20px]">stars</span>
            </div>
            <div class="text-xs">
                <div class="font-extrabold text-amber-950 text-sm">Anda adalah Ketua Tim (Lead Developer) pada Proyek Ini</div>
                <p class="mt-0.5 text-amber-800 leading-relaxed">
                    Sebagai koordinator tunggal proyek, Anda memiliki wewenang untuk <strong>memperbarui progres sprint</strong>, <strong>menambahkan dokumen perancangan / tautan Google Docs</strong>, serta <strong>melaporkan kendala penundaan</strong> jika ada hambatan teknis.
                </p>
            </div>
        </div>
    @else
        @php
            $leadUser = $project->tim->where('peran', 'ketua_tim')->first()?->user;
        @endphp
        <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200 text-blue-900 flex items-start gap-3.5 shadow-2xs">
            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[20px]">person</span>
            </div>
            <div class="text-xs">
                <div class="font-bold text-blue-950 text-sm">Anda Bertugas sebagai Anggota Tim pada Proyek Ini</div>
                <p class="mt-0.5 text-blue-800 leading-relaxed">
                    Pelaporan progres harian dan administrasi dokumen dikoordinasikan secara terpusat oleh Ketua Tim: <strong>{{ $leadUser->name ?? 'Lead Developer' }}</strong>. Anda dapat melihat seluruh spesifikasi kebutuhan dan dokumen kerja di bawah ini.
                </p>
            </div>
        </div>
    @endif

    <!-- Project Overview Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-7">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-2 max-w-2xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">
                    {{ $project->aplikasi->instansi->nama_instansi ?? 'Pemerintah Provinsi Riau' }}
                </span>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">{{ $project->nama_project }}</h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ $project->aplikasi->deskripsi ?? ($project->pengajuan->deskripsi_kebutuhan ?? 'Tidak ada deskripsi.') }}
                </p>
            </div>

            <!-- Quick Action Buttons for Lead -->
            @if ($isLead)
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <button type="button" onclick="openModalUpdateProgres()" class="px-3.5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">trending_up</span>
                        <span>Update Capaian Sprint</span>
                    </button>
                    <button type="button" onclick="openModalTambahDokumen()" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">post_add</span>
                        <span>Tambah Dokumen / Link Docs</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Meta Details Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Tanggal Mulai</span>
                <span class="font-bold text-slate-800">{{ $project->tanggal_mulai ? $project->tanggal_mulai->format('d M Y') : '-' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Target Selesai</span>
                <span class="font-bold text-slate-800">{{ $project->tanggal_selesai ? $project->tanggal_selesai->format('d M Y') : '-' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">PIC OPD Pemohon</span>
                <span class="font-bold text-slate-800 truncate block">{{ $project->pengajuan->nama_pemohon ?? '-' }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Kontak WhatsApp OPD</span>
                @if ($project->pengajuan?->no_hp_pemohon)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $project->pengajuan->no_hp_pemohon) }}" target="_blank" class="font-bold text-emerald-700 hover:underline flex items-center gap-1">
                        <span>{{ $project->pengajuan->no_hp_pemohon }}</span>
                        <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                    </a>
                @else
                    <span class="text-slate-400">-</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Team Members Bar -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">group</span>
                <span>Susunan Personil Tim Proyek</span>
            </h3>
            <span class="text-xs text-slate-400 font-semibold">{{ $project->tim->count() }} Orang</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-4">
            @foreach ($project->tim as $member)
                <div class="p-3 rounded-xl border {{ $member->peran === 'ketua_tim' ? 'border-amber-200 bg-amber-50/30' : 'border-slate-200/80 bg-slate-50/50' }} flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg {{ $member->peran === 'ketua_tim' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-700' }} font-bold text-xs flex items-center justify-center shrink-0">
                        {{ substr($member->user->name ?? 'U', 0, 2) }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-slate-900 truncate">{{ $member->user->name }}</span>
                            @if ($member->user_id === auth()->id())
                                <span class="text-[9px] font-bold text-[#0D47A1] bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 shrink-0">Saya</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between mt-0.5 text-[10px]">
                            <span class="text-slate-400 truncate">{{ $member->user->jabatan ?? 'Programmer' }}</span>
                            @if ($member->peran === 'ketua_tim')
                                <span class="font-extrabold text-amber-800">Ketua Tim</span>
                            @else
                                <span class="text-slate-500 font-medium">Anggota</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Sprint Progress & Milestones -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">checklist</span>
                    <span>Tahapan Pengerjaan & Sprint Milestones</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pemantauan target modul dari perencanaan hingga selesai</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 block font-semibold uppercase">Total Progres</span>
                    <span class="text-base font-extrabold text-[#0D47A1]">{{ $project->progres_total }}%</span>
                </div>
                @if ($isLead)
                    <button type="button" onclick="openModalTambahTahapan()" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer" title="Tambah Tahapan Baru">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Big Progress Bar -->
        <div class="px-6 pt-5 pb-3">
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                <div class="bg-[#0D47A1] h-3 rounded-full transition-all duration-500" style="width: {{ $project->progres_total }}%"></div>
            </div>
        </div>

        <!-- Milestones List -->
        <div class="divide-y divide-slate-100">
            @forelse ($project->tahapan as $tahap)
                <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <div class="flex items-center gap-3.5">
                        <div class="w-8 h-8 rounded-lg {{ $tahap->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : ($tahap->status === 'dalam_pengerjaan' ? 'bg-blue-100 text-[#0D47A1]' : 'bg-slate-100 text-slate-400') }} flex items-center justify-center font-bold text-xs shrink-0">
                            {{ $tahap->urutan }}
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ $tahap->nama_tahapan }}</h4>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Status: 
                                <span class="font-semibold {{ $tahap->status === 'selesai' ? 'text-emerald-700' : ($tahap->status === 'dalam_pengerjaan' ? 'text-[#0D47A1]' : 'text-slate-500') }}">
                                    {{ ucfirst(str_replace('_', ' ', $tahap->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-24 hidden sm:block">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $tahap->status === 'selesai' ? 'bg-emerald-500' : 'bg-[#0D47A1]' }} h-1.5 rounded-full" style="width: {{ $tahap->persentase }}%"></div>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold text-slate-800 w-10 text-right">{{ $tahap->persentase }}%</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada tahapan sprint yang didefinisikan untuk proyek ini.
                    @if ($isLead)
                        <button type="button" onclick="openModalTambahTahapan()" class="text-[#0D47A1] hover:underline font-bold block mt-1 mx-auto">
                            + Tambah Tahapan Pertama
                        </button>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <!-- Dokumen Perancangan Sistem & LINK DOCS INTEGRATION -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">description</span>
                    <span>Dokumen Perancangan Sistem & Tautan Cloud Docs</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Dokumen SRS, ERD, arsitektur, dan tautan kerja Google Docs / Figma</p>
            </div>

            @if ($isLead)
                <button type="button" onclick="openModalTambahDokumen()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add_link</span>
                    <span>Tambah Dokumen / Link Docs</span>
                </button>
            @endif
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
                                    <span class="font-mono text-slate-400 truncate max-w-xs sm:max-w-md inline-block align-bottom">{{ $doc->file_dokumen }}</span>
                                @else
                                    <span class="text-slate-400">Berkas Tersimpan:</span>
                                    <span class="font-mono text-slate-600">{{ basename($doc->file_dokumen) }}</span>
                                @endif
                            </div>
                            @if ($doc->catatan)
                                <div class="mt-2 text-xs p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-600">
                                    <strong>Catatan Evaluasi APTIKA:</strong> {{ $doc->catatan }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Direct Access Button -->
                    <div class="shrink-0 sm:self-center">
                        @if ($isExternalLink)
                            <a href="{{ $doc->file_dokumen }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
                                <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                <span>Buka di Google Docs / Cloud</span>
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                                <span>Unduh Berkas</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada dokumen perancangan sistem yang diunggah atau ditautkan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Panel Feedback Uji Fungsional & Keamanan CSIRT (Feedback Loop) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Hasil Uji Fungsional (APTIKA) -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-teal-600">fact_check</span>
                        <span>Audit Uji Fungsional (APTIKA)</span>
                    </h3>
                    @if ($project->testingAplikasi->isNotEmpty())
                        @php $latestTest = $project->testingAplikasi->first(); @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                            {{ $latestTest->status_validasi === 'lulus' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $latestTest->status_validasi }}
                        </span>
                    @else
                        <span class="text-[10px] text-slate-400">Belum diuji</span>
                    @endif
                </div>

                @if ($project->testingAplikasi->isNotEmpty())
                    @php $test = $project->testingAplikasi->first(); @endphp
                    <div class="text-xs text-slate-600 space-y-2">
                        <p class="leading-relaxed">{{ $test->keterangan }}</p>
                        @if ($test->catatan_validasi)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-700">
                                <span class="font-bold text-slate-800 block mb-0.5">Catatan Perbaikan Tim:</span>
                                {{ $test->catatan_validasi }}
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Proyek belum memasuki tahapan uji fungsional oleh Bidang APTIKA.</p>
                @endif
            </div>
        </div>

        <!-- Hasil Uji Keamanan (CSIRT) -->
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-purple-600">security</span>
                        <span>Uji Keamanan & Pentest (CSIRT)</span>
                    </h3>
                    @if ($project->pengujianKeamanan->isNotEmpty())
                        @php $latestSec = $project->pengujianKeamanan->first(); @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                            {{ $latestSec->hasil === 'lulus' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                            {{ $latestSec->hasil }}
                        </span>
                    @else
                        <span class="text-[10px] text-slate-400">Belum diuji</span>
                    @endif
                </div>

                @if ($project->pengujianKeamanan->isNotEmpty())
                    @php $sec = $project->pengujianKeamanan->first(); @endphp
                    <div class="text-xs text-slate-600 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-400">Tingkat Risiko:</span>
                            <span class="font-bold uppercase {{ $sec->tingkat_risiko === 'aman' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $sec->tingkat_risiko }}
                            </span>
                        </div>
                        @if ($sec->catatan_kerentanan)
                            <div class="p-3 rounded-xl bg-purple-50/60 border border-purple-100 text-[11px] text-purple-900">
                                <span class="font-bold text-purple-950 block mb-0.5">Temuan Kerentanan:</span>
                                {{ $sec->catatan_kerentanan }}
                            </div>
                        @endif
                        @if ($sec->rekomendasi)
                            <div class="text-[11px] text-slate-600">
                                <strong>Rekomendasi Penambalan:</strong> {{ $sec->rekomendasi }}
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Pengujian penetrasi kerentanan belum dilaksanakan oleh CSIRT Persandian.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Kendala & Penundaan Proyek -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-rose-600">warning</span>
                <span>Kendala & Status Penundaan Proyek</span>
            </h3>

            @if ($isLead && $project->status !== 'delayed')
                <button type="button" onclick="openModalPenundaan()" class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[14px]">flag</span>
                    <span>Ajukan Penundaan</span>
                </button>
            @endif
        </div>

        @if ($project->penundaan->isNotEmpty())
            <div class="space-y-3">
                @foreach ($project->penundaan as $delay)
                    <div class="p-4 rounded-xl bg-rose-50/50 border border-rose-200 text-xs text-rose-950">
                        <div class="flex items-center justify-between font-bold mb-1">
                            <span>Permohonan Penundaan #{{ $delay->id }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-white border border-rose-200">
                                {{ $delay->status }}
                            </span>
                        </div>
                        <p class="text-rose-900 mt-1 leading-relaxed">{{ $delay->alasan }}</p>
                        <div class="text-[10px] text-rose-700/80 mt-2">
                            Diajukan pada: {{ $delay->created_at->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 italic">Tidak ada catatan penundaan pada proyek ini. Pengerjaan berjalan sesuai jadwal.</p>
        @endif
    </div>

</div>

<!-- ================= MODALS (KHUSUS KETUA TIM) ================= -->
@if ($isLead)
    <!-- Modal 1: Update Progres Total & Tahapan Sprint -->
    <div id="modalUpdateProgres" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" onclick="closeModalUpdateProgres()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white border border-slate-200 shadow-2xl p-6 z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">trending_up</span>
                        <span>Update Progres & Tahapan Sprint</span>
                    </h3>
                    <button type="button" onclick="closeModalUpdateProgres()" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('tim.project.progres', $project) }}" class="space-y-4 pt-4">
                    @csrf
                    @method('PATCH')

                    <!-- Progres Total Slider/Input -->
                    <div>
                        <label for="progres_total" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Akumulasi Progres Total (%) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input 
                                type="number" 
                                name="progres_total" 
                                id="progres_total" 
                                min="0" 
                                max="100" 
                                value="{{ old('progres_total', $project->progres_total) }}" 
                                class="block w-24 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-center focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]"
                                required
                            >
                            <span class="text-xs text-slate-500">Persentase capaian seluruh modul aplikasi</span>
                        </div>
                    </div>

                    <!-- Tahapan Breakdown -->
                    @if ($project->tahapan->isNotEmpty())
                        <div class="pt-3 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Detail Persentase Tahapan Sprint
                            </label>
                            <div class="space-y-3 max-h-56 overflow-y-auto pr-1">
                                @foreach ($project->tahapan as $idx => $tahap)
                                    <input type="hidden" name="tahapan[{{ $idx }}][id]" value="{{ $tahap->id }}">
                                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 text-xs space-y-2">
                                        <div class="font-bold text-slate-800">{{ $tahap->urutan }}. {{ $tahap->nama_tahapan }}</div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-[10px] text-slate-400 block mb-0.5">Persentase (%)</label>
                                                <input 
                                                    type="number" 
                                                    name="tahapan[{{ $idx }}][persentase]" 
                                                    min="0" 
                                                    max="100" 
                                                    value="{{ $tahap->persentase }}" 
                                                    class="block w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-[#0D47A1]"
                                                >
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-slate-400 block mb-0.5">Status</label>
                                                <select 
                                                    name="tahapan[{{ $idx }}][status]" 
                                                    class="block w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-[#0D47A1]"
                                                >
                                                    <option value="belum_mulai" {{ $tahap->status === 'belum_mulai' ? 'selected' : '' }}>Belum Mulai</option>
                                                    <option value="dalam_pengerjaan" {{ $tahap->status === 'dalam_pengerjaan' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                                                    <option value="selesai" {{ $tahap->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeModalUpdateProgres()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-xs">
                            Simpan Progres
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Tambah Dokumen Perancangan / Link Google Docs -->
    <div id="modalTambahDokumen" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" onclick="closeModalTambahDokumen()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white border border-slate-200 shadow-2xl p-6 z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-blue-600">post_add</span>
                        <span>Tambah Dokumen / Tautan Cloud Docs</span>
                    </h3>
                    <button type="button" onclick="closeModalTambahDokumen()" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('tim.project.dokumen', $project) }}" enctype="multipart/form-data" class="space-y-4 pt-4">
                    @csrf

                    <!-- Jenis Dokumen -->
                    <div>
                        <label for="jenis_dokumen" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Jenis Dokumen Perancangan <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_dokumen" id="jenis_dokumen" class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]" required>
                            <option value="srs">SRS (Software Requirements Specification)</option>
                            <option value="erd">ERD (Entity Relationship Diagram / Skema Basis Data)</option>
                            <option value="wireframe">Wireframe / Mockup UI/UX Design</option>
                            <option value="arsitektur">Arsitektur Sistem & Topologi Jaringan</option>
                            <option value="lainnya">Dokumen Teknis Lainnya</option>
                        </select>
                    </div>

                    <!-- Sumber Dokumen (Pilihan Link vs File) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Metode Penyediaan Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition select-none">
                                <input type="radio" name="tipe_sumber" value="link" checked onchange="toggleSumberDokumen(this.value)" class="text-[#0D47A1] focus:ring-[#0D47A1]">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 block">Tautan Cloud Docs</span>
                                    <span class="text-[10px] text-slate-400">Google Docs / Figma / Notion</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition select-none">
                                <input type="radio" name="tipe_sumber" value="file" onchange="toggleSumberDokumen(this.value)" class="text-[#0D47A1] focus:ring-[#0D47A1]">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 block">Unggah Berkas</span>
                                    <span class="text-[10px] text-slate-400">File PDF / Word / ZIP</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Input Tautan (Link) -->
                    <div id="sectionLinkDokumen">
                        <label for="link_dokumen" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            URL / Link Dokumen Online <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="url" 
                            name="link_dokumen" 
                            id="link_dokumen" 
                            placeholder="https://docs.google.com/document/d/... atau https://figma.com/file/..." 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]"
                        >
                        <p class="text-[10px] text-slate-400 mt-1">Pastikan hak akses tautan telah diset ke <em>'Anyone with link can view'</em> agar dapat ditelaah oleh APTIKA.</p>
                    </div>

                    <!-- Input Berkas (File) -->
                    <div id="sectionFileDokumen" class="hidden">
                        <label for="file_dokumen" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Pilih Berkas Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            name="file_dokumen" 
                            id="file_dokumen" 
                            accept=".pdf,.doc,.docx,.zip" 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]"
                        >
                        <p class="text-[10px] text-slate-400 mt-1">Format PDF, Word, atau ZIP. Maksimal 20 MB.</p>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label for="catatan_dok" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Catatan untuk Penelaah (Opsional)
                        </label>
                        <textarea 
                            name="catatan" 
                            id="catatan_dok" 
                            rows="2" 
                            placeholder="Contoh: Dokumen spesifikasi hasil revisi sprint 1..." 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]"
                        ></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeModalTambahDokumen()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-xs">
                            Simpan Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 3: Tambah Tahapan Sprint Baru -->
    <div id="modalTambahTahapan" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" onclick="closeModalTambahTahapan()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-2xl p-6 z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-emerald-600">playlist_add</span>
                        <span>Tambah Tahapan Sprint Baru</span>
                    </h3>
                    <button type="button" onclick="closeModalTambahTahapan()" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('tim.project.tahapan', $project) }}" class="space-y-4 pt-4">
                    @csrf
                    <div>
                        <label for="nama_tahapan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Tahapan / Milestone <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_tahapan" 
                            id="nama_tahapan" 
                            placeholder="Contoh: Pembuatan Modul Pembayaran QRIS" 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]" 
                            required
                        >
                    </div>

                    <div>
                        <label for="persentase_awal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Persentase Selesai (%)
                        </label>
                        <input 
                            type="number" 
                            name="persentase" 
                            id="persentase_awal" 
                            min="0" 
                            max="100" 
                            value="0" 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]"
                        >
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeModalTambahTahapan()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs">
                            Tambah Tahapan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 4: Pengajuan Penundaan Proyek -->
    <div id="modalPenundaan" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" onclick="closeModalPenundaan()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white border border-slate-200 shadow-2xl p-6 z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-rose-600">flag</span>
                        <span>Pengajuan Penundaan Proyek ke APTIKA</span>
                    </h3>
                    <button type="button" onclick="closeModalPenundaan()" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('tim.project.penundaan', $project) }}" enctype="multipart/form-data" class="space-y-4 pt-4">
                    @csrf
                    <div>
                        <label for="alasan_delay" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan & Rincian Kendala Lapangan <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            name="alasan" 
                            id="alasan_delay" 
                            rows="4" 
                            placeholder="Jelaskan kendala teknis atau eksternal yang menyebabkan proyek tertunda (minimal 10 karakter)..." 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500" 
                            required
                        ></textarea>
                    </div>

                    <div>
                        <label for="file_surat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Surat / Bukti Lampiran Kendala (PDF Opsional)
                        </label>
                        <input 
                            type="file" 
                            name="file_surat" 
                            id="file_surat" 
                            accept=".pdf" 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeModalPenundaan()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">
                            Kirim Permohonan Penundaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Kontrol Modal & Toggle Sumber Dokumen -->
    <script>
        function openModalUpdateProgres() { document.getElementById('modalUpdateProgres').classList.remove('hidden'); }
        function closeModalUpdateProgres() { document.getElementById('modalUpdateProgres').classList.add('hidden'); }

        function openModalTambahDokumen() { document.getElementById('modalTambahDokumen').classList.remove('hidden'); }
        function closeModalTambahDokumen() { document.getElementById('modalTambahDokumen').classList.add('hidden'); }

        function openModalTambahTahapan() { document.getElementById('modalTambahTahapan').classList.remove('hidden'); }
        function closeModalTambahTahapan() { document.getElementById('modalTambahTahapan').classList.add('hidden'); }

        function openModalPenundaan() { document.getElementById('modalPenundaan').classList.remove('hidden'); }
        function closeModalPenundaan() { document.getElementById('modalPenundaan').classList.add('hidden'); }

        function toggleSumberDokumen(type) {
            const secLink = document.getElementById('sectionLinkDokumen');
            const secFile = document.getElementById('sectionFileDokumen');
            if (type === 'link') {
                secLink.classList.remove('hidden');
                secFile.classList.add('hidden');
            } else {
                secLink.classList.add('hidden');
                secFile.classList.remove('hidden');
            }
        }
    </script>
@endif

@endsection
