@extends('layouts.opd')

@section('title', 'Buat Permohonan Baru')
@section('page-title', 'Formulir Pengajuan Layanan Aplikasi')
@section('breadcrumb', 'Layanan Aplikasi / Pengajuan Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Notice Card -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100/80 shadow-xs flex items-start gap-4">
        <span class="material-symbols-outlined text-[#0D47A1] text-[28px] shrink-0 mt-0.5">verified_user</span>
        <div class="text-xs text-slate-700 leading-relaxed">
            <span class="font-bold text-[#0D47A1] block text-sm mb-1">Standarisasi & Regulasi Layanan SPBE Daerah</span>
            Setiap usulan layanan difasilitasi oleh Dinas Kominfo Provinsi Riau dan diproses melalui alur workflow tahapan kerja resmi. Pastikan seluruh berkas persyaratan dan konfirmasi tahapan awal terisi dengan benar.
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('opd.pengajuan.store') }}" enctype="multipart/form-data" class="space-y-8" id="formPengajuan">
            @csrf

            <!-- 1. Pilihan Jenis Layanan Dinamis -->
            <div>
                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">
                    Langkah 1: Pilih Jenis Layanan Fasilitasi <span class="text-rose-500">*</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($layanans as $layananItem)
                        @php
                            $isSelected = ($selectedLayanan && $selectedLayanan->id === $layananItem->id);
                            $isNewApp = $layananItem->isAplikasiBaru();
                            $legacyType = $isNewApp ? 'new_app' : (str_contains($layananItem->slug, 'nonaktif') || str_contains($layananItem->slug, 'deactivation') ? 'app_deactivation' : 'app_development');
                        @endphp
                        <div 
                            class="layanan-card cursor-pointer p-4 rounded-xl border-2 transition select-none flex flex-col justify-between {{ $isSelected ? 'border-[#0D47A1] bg-blue-50/40 ring-2 ring-[#0D47A1]/10' : 'border-slate-200 hover:border-slate-300 bg-white' }}"
                            data-layanan-id="{{ $layananItem->id }}"
                            data-layanan-slug="{{ $layananItem->slug }}"
                            data-is-new="{{ $isNewApp ? '1' : '0' }}"
                            data-legacy-type="{{ $legacyType }}"
                            onclick="selectLayanan({{ $layananItem->id }}, '{{ $layananItem->slug }}', {{ $isNewApp ? 'true' : 'false' }}, '{{ $legacyType }}')"
                        >
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="w-9 h-9 rounded-lg flex items-center justify-center shadow-2xs" style="background-color: {{ $layananItem->warna }}18; color: {{ $layananItem->warna }};">
                                        <span class="material-symbols-outlined text-[22px]">{{ $layananItem->ikon ?? 'category' }}</span>
                                    </span>
                                    <span class="radio-check w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center {{ $isSelected ? 'border-[#0D47A1] bg-[#0D47A1]' : '' }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white {{ $isSelected ? 'block' : 'hidden' }}"></span>
                                    </span>
                                </div>
                                <div class="text-sm font-bold text-slate-900">{{ $layananItem->nama }}</div>
                                <div class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $layananItem->deskripsi }}</div>
                            </div>

                            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <span class="px-2 py-0.5 rounded-full font-semibold bg-slate-100 text-slate-600">
                                    {{ $layananItem->workflows->count() }} Tahapan Alur
                                </span>
                                <span class="font-bold text-[#0D47A1] flex items-center gap-0.5">
                                    <span>Pilih</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="layanan_id" id="layanan_id_input" value="{{ $selectedLayanan?->id }}">
                <input type="hidden" name="jenis_pengajuan" id="jenis_pengajuan_input" value="{{ old('jenis_pengajuan', str_contains($selectedLayanan?->slug ?? '', 'baru') ? 'new_app' : (str_contains($selectedLayanan?->slug ?? '', 'nonaktif') ? 'app_deactivation' : 'app_development')) }}">
                
                @error('layanan_id')
                    <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Visualisasi Alur Workflow Tahapan Layanan yang Dipilih -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Langkah 2: Susunan Alur Workflow Layanan Terpilih
                    </label>
                    <span class="text-[11px] text-slate-400">Tahapan resmi yang akan dilalui usulan ini</span>
                </div>

                @foreach ($layanans as $layananItem)
                    @php
                        $isSelected = ($selectedLayanan && $selectedLayanan->id === $layananItem->id);
                    @endphp
                    <div 
                        class="layanan-wf-timeline p-5 rounded-2xl bg-gradient-to-r from-slate-50 to-blue-50/30 border border-slate-200/90 transition" 
                        id="wf_timeline_{{ $layananItem->id }}"
                        style="{{ $isSelected ? '' : 'display: none;' }}"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-[#0D47A1] flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[16px]">account_tree</span>
                                </span>
                                <span class="text-xs font-bold text-slate-800">
                                    Alur Workflow: <strong class="text-[#0D47A1]">{{ $layananItem->nama }}</strong>
                                </span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-[#0D47A1] border border-blue-200 self-start sm:self-auto">
                                {{ $layananItem->workflows->count() }} Tahapan Tersusun
                            </span>
                        </div>

                        @if ($layananItem->workflows->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($layananItem->workflows as $lw)
                                    <div class="p-3.5 rounded-xl border {{ $loop->first ? 'bg-white border-[#0D47A1] ring-2 ring-[#0D47A1]/10 shadow-xs' : 'bg-white/80 border-slate-200/80' }} transition flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-lg {{ $loop->first ? 'bg-[#0D47A1] text-white font-bold' : 'bg-slate-100 text-slate-500 font-semibold' }} flex items-center justify-center text-[11px]">
                                                        {{ $lw->urutan }}
                                                    </span>
                                                    <span class="text-[10px] font-bold {{ $loop->first ? 'text-[#0D47A1]' : 'text-slate-400' }} uppercase tracking-wider">
                                                        Tahap {{ $lw->urutan }}
                                                    </span>
                                                </div>
                                                @if ($loop->first)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Saat Ini (Form)
                                                    </span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium text-slate-400 bg-slate-100">
                                                        Tahap Lanjutan
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs font-bold text-slate-900 leading-snug">
                                                {{ $lw->workflow->nama }}
                                            </div>
                                            @if ($lw->workflow->deskripsi)
                                                <div class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                                    {{ $lw->workflow->deskripsi }}
                                                </div>
                                            @endif
                                        </div>

                                        @if ($loop->first && $lw->workflow->fields->isNotEmpty())
                                            <div class="mt-2.5 pt-2 border-t border-slate-100 text-[10px] font-medium text-[#0D47A1] flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]">checklist</span>
                                                <span>{{ $lw->workflow->fields->count() }} Isian / Berkas Persyaratan</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/50 text-xs text-amber-800 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">warning</span>
                                <span>Layanan ini belum memiliki tahapan alur workflow aktif.</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- 3. Informasi Aplikasi -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Langkah 3: Data Sistem & Kebutuhan
                </div>

                @php
                    $isInitiallyNew = str_contains($selectedLayanan?->slug ?? '', 'baru') || old('jenis_pengajuan') === 'new_app';
                @endphp

                <!-- Input Nama Usulan Aplikasi (Jika layanan sistem baru atau belum terdaftar) -->
                <div id="fieldNamaAplikasi" class="{{ $isInitiallyNew ? 'block' : 'hidden' }}">
                    <label for="nama_aplikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Usulan Aplikasi <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_aplikasi" 
                        id="nama_aplikasi" 
                        value="{{ old('nama_aplikasi') }}" 
                        placeholder="Contoh: Sistem Informasi Manajemen RSUD Petala Bumi (SIMRS)" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_aplikasi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    @error('nama_aplikasi')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Select Aplikasi Eksisting (Jika layanan pengembangan / penonaktifan) -->
                <div id="fieldPilihAplikasi" class="{{ $isInitiallyNew ? 'hidden' : 'block' }}">
                    <label for="aplikasi_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pilih Aplikasi Terdaftar <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="aplikasi_id" 
                        id="aplikasi_id" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('aplikasi_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                        <option value="">-- Pilih Aplikasi Milik Perangkat Daerah Anda --</option>
                        @foreach ($aplikasis as $app)
                            <option value="{{ $app->id }}" {{ old('aplikasi_id') == $app->id ? 'selected' : '' }}>
                                {{ $app->nama_aplikasi }} ({{ $app->url_aplikasi ?? 'URL Internal' }})
                            </option>
                        @endforeach
                    </select>
                    @error('aplikasi_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Kebutuhan & Ruang Lingkup -->
                <div>
                    <label for="deskripsi_kebutuhan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Deskripsi Kebutuhan & Ruang Lingkup <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="deskripsi_kebutuhan" 
                        id="deskripsi_kebutuhan" 
                        rows="4" 
                        placeholder="Jelaskan proses bisnis, tujuan sistem, target pengguna, dan latar belakang usulan secara rinci..." 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('deskripsi_kebutuhan') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >{{ old('deskripsi_kebutuhan') }}</textarea>
                    @error('deskripsi_kebutuhan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 4. Narahubung Pemohon -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Langkah 4: Narahubung & Pejabat Pemohon
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_pemohon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Pejabat / PIC <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_pemohon" 
                            id="nama_pemohon" 
                            value="{{ old('nama_pemohon', auth()->user()->name) }}" 
                            placeholder="Nama Lengkap Pejabat / Pengelola TIK" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_pemohon') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                            required
                        >
                        @error('nama_pemohon')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_hp_pemohon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="no_hp_pemohon" 
                            id="no_hp_pemohon" 
                            value="{{ old('no_hp_pemohon', auth()->user()->no_hp) }}" 
                            placeholder="0812xxxxxxxx" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('no_hp_pemohon') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                            required
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk konfirmasi jadwal telaah & koordinasi rapat teknis.</p>
                        @error('no_hp_pemohon')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 5. Berkas Persyaratan & Field Dinamis Tahap 1 Workflow Sesuai Layanan -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Langkah 5: Berkas Persyaratan & Checklist Tahap 1
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Formulir dan berkas yang dipersyaratkan pada tahapan awal alur layanan yang Anda pilih.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="material-symbols-outlined text-[14px]">security</span>
                        <span>Folder Terisolasi</span>
                    </span>
                </div>

                @foreach ($layanans as $layananItem)
                    @php
                        $isSelected = ($selectedLayanan && $selectedLayanan->id === $layananItem->id);
                        $firstLw = $layananItem->workflows->first();
                        $firstWf = $firstLw?->workflow;
                    @endphp
                    <div 
                        class="layanan-step1-pane space-y-4 transition" 
                        id="step1_pane_{{ $layananItem->id }}"
                        style="{{ $isSelected ? '' : 'display: none;' }}"
                    >
                        @if ($firstWf)
                            <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-100 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">fact_check</span>
                                    <span>Persyaratan Tahap 1: <strong>{{ $firstWf->nama }}</strong></span>
                                </div>
                                <span class="text-[11px] text-[#0D47A1] font-semibold">
                                    Layanan {{ $layananItem->nama }}
                                </span>
                            </div>

                            @if ($firstWf->fields->isNotEmpty())
                                <div class="space-y-4">
                                    @foreach ($firstWf->fields as $field)
                                        @if ($field->tipe === 'file_upload')
                                            @php
                                                $masterDoc = $field->masterDokumen;
                                                $inputName = $masterDoc ? "dokumen[{$masterDoc->id}]" : "workflow_files[{$field->id}]";
                                                $inputId = "file_field_{$layananItem->id}_{$field->id}";
                                                $format = $masterDoc ? $masterDoc->format_diizinkan : 'pdf,doc,docx';
                                                $maxKb = $masterDoc ? $masterDoc->max_size_kb : 10240;
                                            @endphp
                                            <div class="p-5 rounded-xl border border-slate-200/90 bg-slate-50/40 space-y-3">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-[#0D47A1] flex items-center justify-center font-bold text-xs shrink-0">
                                                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                                                        </span>
                                                        <div>
                                                            <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                                                <span>{{ $field->label }}</span>
                                                                @if ($field->wajib)
                                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Wajib</span>
                                                                @else
                                                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">Opsional</span>
                                                                @endif
                                                            </h4>
                                                        </div>
                                                    </div>

                                                    @if ($masterDoc && $masterDoc->file_template)
                                                        <a href="{{ asset('storage/' . $masterDoc->file_template) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shrink-0">
                                                            <span class="material-symbols-outlined text-[15px]">file_download</span>
                                                            <span>Unduh Format Blangko</span>
                                                        </a>
                                                    @endif
                                                </div>

                                                @if ($field->keterangan || ($masterDoc && $masterDoc->deskripsi))
                                                    <p class="text-xs text-slate-600 bg-white/70 p-2.5 rounded-lg border border-slate-200/60 leading-relaxed">
                                                        {{ $field->keterangan ?? $masterDoc->deskripsi }}
                                                    </p>
                                                @endif

                                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                    <input 
                                                        type="file" 
                                                        name="{{ $inputName }}" 
                                                        id="{{ $inputId }}"
                                                        data-wajib="{{ $field->wajib ? '1' : '0' }}"
                                                        accept="{{ '.' . str_replace([' ', ','], ',.', $format) }}"
                                                        class="step1-input block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0D47A1] file:text-white hover:file:bg-[#1565C0] cursor-pointer border border-slate-200 rounded-xl bg-white p-2"
                                                        {{ ($isSelected && $field->wajib) ? 'required' : '' }}
                                                        {{ $isSelected ? '' : 'disabled' }}
                                                    >
                                                    <div class="text-[11px] text-slate-400 shrink-0">
                                                        Format: <strong class="text-slate-600 uppercase">{{ $format }}</strong> (Maks. {{ number_format($maxKb / 1024, 1) }} MB)
                                                    </div>
                                                </div>
                                                @error($masterDoc ? "dokumen.{$masterDoc->id}" : "workflow_files.{$field->id}")
                                                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @elseif ($field->tipe === 'checklist')
                                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-blue-50/30 transition">
                                                <label class="flex items-start gap-3 cursor-pointer">
                                                    <input 
                                                        type="checkbox" 
                                                        name="fields[{{ $field->id }}]" 
                                                        value="1" 
                                                        data-wajib="{{ $field->wajib ? '1' : '0' }}"
                                                        class="step1-input mt-0.5 w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1]"
                                                        {{ ($isSelected && $field->wajib) ? 'required' : '' }}
                                                        {{ $isSelected ? '' : 'disabled' }}
                                                        {{ old('fields.' . $field->id) ? 'checked' : '' }}
                                                    >
                                                    <div class="text-xs">
                                                        <span class="font-bold text-slate-800">{{ $field->label }}</span>
                                                        @if ($field->wajib)
                                                            <span class="text-rose-500 font-bold">*</span>
                                                        @endif
                                                        @if ($field->keterangan)
                                                            <p class="text-slate-500 mt-0.5 leading-relaxed">{{ $field->keterangan }}</p>
                                                        @endif
                                                    </div>
                                                </label>
                                                @error('fields.' . $field->id)
                                                    <p class="text-xs text-rose-600 mt-1 pl-7">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @elseif ($field->tipe === 'text')
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                                    {{ $field->label }} @if ($field->wajib) <span class="text-rose-500">*</span> @endif
                                                </label>
                                                <textarea 
                                                    name="fields[{{ $field->id }}]" 
                                                    rows="2" 
                                                    data-wajib="{{ $field->wajib ? '1' : '0' }}"
                                                    placeholder="{{ $field->keterangan ?? 'Masukkan isian...' }}" 
                                                    class="step1-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                                                    {{ ($isSelected && $field->wajib) ? 'required' : '' }}
                                                    {{ $isSelected ? '' : 'disabled' }}
                                                >{{ old('fields.' . $field->id) }}</textarea>
                                                @error('fields.' . $field->id)
                                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="p-5 rounded-xl border border-blue-200 bg-blue-50/40 text-xs text-slate-600 flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[22px] text-[#0D47A1]">check_circle</span>
                                    <div>
                                        <span class="font-bold text-slate-800 block">Tidak Memerlukan Berkas Persyaratan Khusus</span>
                                        Tahapan awal (<strong>{{ $firstWf->nama }}</strong>) pada layanan ini siap diajukan secara langsung tanpa lampiran berkas khusus tambahan.
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/50 text-xs text-amber-800">
                                Layanan ini belum memiliki alur tahapan workflow aktif.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('opd.pengajuan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-bold shadow-md shadow-[#0D47A1]/20 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Kirim Permohonan ke Kominfo</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectLayanan(layananId, layananSlug, isNewApp, legacyType) {
        // 1. Highlight selected card
        document.querySelectorAll('.layanan-card').forEach(card => {
            const id = card.getAttribute('data-layanan-id');
            const checkRadio = card.querySelector('.radio-check');
            const dot = checkRadio ? checkRadio.querySelector('span') : null;

            if (id == layananId) {
                card.classList.add('border-[#0D47A1]', 'bg-blue-50/40', 'ring-2', 'ring-[#0D47A1]/10');
                card.classList.remove('border-slate-200', 'bg-white');
                if (checkRadio) {
                    checkRadio.classList.add('border-[#0D47A1]', 'bg-[#0D47A1]');
                    checkRadio.classList.remove('border-slate-300');
                }
                if (dot) dot.classList.remove('hidden');
            } else {
                card.classList.remove('border-[#0D47A1]', 'bg-blue-50/40', 'ring-2', 'ring-[#0D47A1]/10');
                card.classList.add('border-slate-200', 'bg-white');
                if (checkRadio) {
                    checkRadio.classList.remove('border-[#0D47A1]', 'bg-[#0D47A1]');
                    checkRadio.classList.add('border-slate-300');
                }
                if (dot) dot.classList.add('hidden');
            }
        });

        // 2. Set hidden inputs
        const hiddenLayanan = document.getElementById('layanan_id_input');
        if (hiddenLayanan) hiddenLayanan.value = layananId;
        const hiddenJenis = document.getElementById('jenis_pengajuan_input');
        if (hiddenJenis) hiddenJenis.value = legacyType;

        // 3. Toggle Workflow Stepper Preview
        document.querySelectorAll('.layanan-wf-timeline').forEach(el => {
            el.style.display = (el.id === 'wf_timeline_' + layananId) ? '' : 'none';
        });

        // 4. Toggle Step 1 Fields & manage disabled/required
        document.querySelectorAll('.layanan-step1-pane').forEach(pane => {
            const isActive = (pane.id === 'step1_pane_' + layananId);
            pane.style.display = isActive ? '' : 'none';

            pane.querySelectorAll('.step1-input').forEach(input => {
                if (isActive) {
                    input.removeAttribute('disabled');
                    if (input.getAttribute('data-wajib') === '1') {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                } else {
                    input.setAttribute('disabled', 'disabled');
                    input.removeAttribute('required');
                }
            });
        });

        // 5. Toggle App Name vs Select Existing App
        const fieldNama = document.getElementById('fieldNamaAplikasi');
        const inputNama = document.getElementById('nama_aplikasi');
        const fieldPilih = document.getElementById('fieldPilihAplikasi');
        const selectApp = document.getElementById('aplikasi_id');

        if (isNewApp) {
            if (fieldNama) fieldNama.classList.remove('hidden');
            if (inputNama) inputNama.setAttribute('required', 'required');
            if (fieldPilih) fieldPilih.classList.add('hidden');
            if (selectApp) selectApp.removeAttribute('required');
        } else {
            if (fieldNama) fieldNama.classList.add('hidden');
            if (inputNama) inputNama.removeAttribute('required');
            if (fieldPilih) fieldPilih.classList.remove('hidden');
            if (selectApp) selectApp.setAttribute('required', 'required');
        }

        // 6. Push URL query param for easy sharing/refreshing
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('layanan', layananSlug);
            window.history.replaceState(null, '', url);
        }
    }

    // Inisialisasi awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        const activeLayananId = document.getElementById('layanan_id_input').value;
        const activeCard = document.querySelector(`.layanan-card[data-layanan-id="${activeLayananId}"]`);
        if (activeCard) {
            const slug = activeCard.getAttribute('data-layanan-slug');
            const isNew = activeCard.getAttribute('data-is-new') === '1';
            const legacyType = activeCard.getAttribute('data-legacy-type');
            selectLayanan(activeLayananId, slug, isNew, legacyType);
        }
    });
</script>
@endsection
