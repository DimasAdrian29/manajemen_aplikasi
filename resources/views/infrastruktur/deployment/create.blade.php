@extends('layouts.infrastruktur')

@section('title', 'Deploy Aplikasi - ' . $project->nama_project)
@section('page_title', 'Form Deployment & Alokasi Server')
@section('page_subtitle', 'Masukkan konfigurasi domain, server, dan hosting untuk mengaktifkan aplikasi secara publik (LIVE)')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('infrastruktur.deployment.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Antrean Deployment</span>
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
            Tahap: Deployment
        </span>
    </div>

    <!-- Project Info Summary Card -->
    <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <span class="text-xs font-semibold text-[#0D47A1] uppercase tracking-wider block">Target Deployment</span>
                <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $project->nama_project }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Instansi: <strong class="text-slate-700">{{ $project->aplikasi?->instansi?->nama_instansi ?? 'Pemerintah Provinsi Riau' }}</strong> &bull;
                    Aplikasi: <strong class="text-slate-700">{{ $project->aplikasi?->nama_aplikasi ?? '-' }}</strong>
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[11px] text-slate-400 block">Ketua Tim Pengembang</span>
                    <span class="text-xs font-bold text-slate-800">
                        {{ $project->tim->where('peran', 'ketua_tim')->first()?->user?->name ?? 'Belum Ditentukan' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Kode Pengajuan</span>
                <span class="font-bold text-slate-800">{{ $project->pengajuan?->kode_pengajuan ?? '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Hasil Uji Fungsional</span>
                <span class="font-bold text-emerald-700 flex items-center gap-1 mt-0.5">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Lulus Pengujian APTIKA
                </span>
            </div>
            @php
                $latestSecurity = $project->pengujianKeamanan->last();
            @endphp
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Hasil Uji Keamanan (CSIRT)</span>
                @if ($latestSecurity)
                    <span class="font-bold text-emerald-700 flex items-center gap-1 mt-0.5">
                        <span class="material-symbols-outlined text-[16px]">verified_user</span>
                        {{ ucfirst($latestSecurity->hasil) }} (Risiko: {{ ucfirst($latestSecurity->tingkat_risiko) }})
                    </span>
                    @if ($latestSecurity->nomor_surat)
                        <span class="text-[10px] text-slate-500 font-mono block mt-0.5">SKLO: {{ $latestSecurity->nomor_surat }}</span>
                    @endif
                @else
                    <span class="font-bold text-slate-500 flex items-center gap-1 mt-0.5">
                        <span class="material-symbols-outlined text-[16px]">shield</span>
                        Belum Ada Data
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Deployment Form -->
    <form method="POST" action="{{ route('infrastruktur.deployment.store', $project) }}" class="space-y-6">
        @csrf

        <!-- Section 1: Domain & URL Publik -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-[#0D47A1]">language</span>
                <h3 class="text-sm font-bold text-slate-900">1. Konfigurasi Domain & URL Produksi</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Domain <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_domain" value="{{ old('nama_domain', $project->hostingAplikasi?->nama_domain ?? strtolower(str_replace(' ', '', $project->nama_project)) . '.riau.go.id') }}" placeholder="contoh: aplikasi.riau.go.id" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">
                    <p class="text-[11px] text-slate-400 mt-1">Domain resmi sub-domain riau.go.id atau TLD terkait</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        URL Aplikasi Publik <span class="text-rose-500">*</span>
                    </label>
                    <input type="url" name="url_aplikasi" value="{{ old('url_aplikasi', $project->hostingAplikasi?->url_aplikasi ?? 'https://' . strtolower(str_replace(' ', '', $project->nama_project)) . '.riau.go.id') }}" placeholder="https://aplikasi.riau.go.id" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">
                    <p class="text-[11px] text-slate-400 mt-1">Gunakan protokol https:// untuk lingkungan produksi</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Status Hosting <span class="text-rose-500">*</span>
                    </label>
                    <select name="status_hosting" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="aktif" {{ old('status_hosting', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif (Siap Diakses Publik)</option>
                        <option value="persiapan" {{ old('status_hosting') === 'persiapan' ? 'selected' : '' }}>Persiapan (Staging Internal)</option>
                        <option value="maintenance" {{ old('status_hosting') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tanggal Go-Live
                    </label>
                    <input type="date" name="tanggal_live" value="{{ old('tanggal_live', now()->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>
        </div>

        <!-- Section 2: Alokasi Server & Spesifikasi Komputasi -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-cyan-600">dns</span>
                <h3 class="text-sm font-bold text-slate-900">2. Alokasi Server & Kapasitas Resource</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Jenis Hosting <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_hosting" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="vps" {{ old('jenis_hosting', 'vps') === 'vps' ? 'selected' : '' }}>VPS (Virtual Private Server)</option>
                        <option value="container" {{ old('jenis_hosting') === 'container' ? 'selected' : '' }}>Container (Docker / K8s)</option>
                        <option value="cloud" {{ old('jenis_hosting') === 'cloud' ? 'selected' : '' }}>Cloud Server (IaaS)</option>
                        <option value="on_premise" {{ old('jenis_hosting') === 'on_premise' ? 'selected' : '' }}>On-Premise (Data Center Riau)</option>
                        <option value="shared" {{ old('jenis_hosting') === 'shared' ? 'selected' : '' }}>Shared Hosting CPanel</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        IP Address Server
                    </label>
                    <input type="text" name="ip_server" value="{{ old('ip_server', '103.150.12.45') }}" placeholder="contoh: 103.150.12.45" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Host / Hostname Server
                    </label>
                    <input type="text" name="nama_server" value="{{ old('nama_server', 'SRV-TIK-VM01') }}" placeholder="contoh: SRV-PROD-01" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Sistem Operasi
                    </label>
                    <input type="text" name="sistem_operasi" value="{{ old('sistem_operasi', 'Ubuntu 22.04 LTS') }}" placeholder="contoh: Ubuntu 22.04" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        CPU (Core)
                    </label>
                    <input type="number" name="cpu_core" min="1" max="256" value="{{ old('cpu_core', 4) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        RAM (GB)
                    </label>
                    <input type="number" name="ram_gb" min="1" max="1024" value="{{ old('ram_gb', 8) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Storage Disk (GB)
                    </label>
                    <input type="number" name="storage_gb" min="1" max="65535" value="{{ old('storage_gb', 100) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Web Server Stack
                    </label>
                    <input type="text" name="web_server" value="{{ old('web_server', 'Nginx 1.24') }}" placeholder="contoh: Nginx 1.24, Apache 2.4" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Database Engine
                    </label>
                    <input type="text" name="database_server" value="{{ old('database_server', 'MySQL 8.0') }}" placeholder="contoh: MySQL 8.0, PostgreSQL 16" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>
        </div>

        <!-- Section 3: Keamanan & Sertifikat SSL -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-emerald-600">lock</span>
                <h3 class="text-sm font-bold text-slate-900">3. Sertifikat SSL / HTTPS</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="ssl_aktif" value="1" {{ old('ssl_aktif', true) ? 'checked' : '' }} class="w-4 h-4 text-[#0D47A1] rounded border-slate-300 focus:ring-0">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Sertifikat SSL Aktif (HTTPS)</span>
                            <span class="text-[11px] text-slate-500 block">Aktifkan enkripsi TLS/SSL Let's Encrypt atau Sertifikat Pemerintah</span>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Masa Berlaku SSL Sampai
                    </label>
                    <input type="date" name="ssl_kadaluarsa" value="{{ old('ssl_kadaluarsa', now()->addDays(90)->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                    <p class="text-[11px] text-slate-400 mt-1">Sistem akan memberi alert jika SSL &le; 30 hari sebelum kadaluarsa</p>
                </div>
            </div>
        </div>

        <!-- Section 4: Catatan & Keterangan -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-slate-600">description</span>
                <h3 class="text-sm font-bold text-slate-900">4. Keterangan & Catatan Teknis</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Spesifikasi Hosting Lainnya (Opsional)
                    </label>
                    <textarea name="spesifikasi_hosting" rows="3" placeholder="Informasi port internal, reverse proxy, PHP-FPM socket, atau git branch..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">{{ old('spesifikasi_hosting') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Keterangan Tambahan (Opsional)
                    </label>
                    <textarea name="keterangan" rows="3" placeholder="Catatan deployment untuk tim pengembang dan OPD..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">{{ old('keterangan', 'Aplikasi berhasil dideploy ke server produksi TIK Riau.') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                <span class="font-bold text-slate-700">Perhatian:</span> Menekan tombol rilis akan mengubah status proyek menjadi <span class="font-bold text-emerald-700">LIVE</span> dan mengirim notifikasi broadcast.
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('infrastruktur.deployment.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition text-center">
                    Batal
                </a>
                <button type="submit" onclick="return confirm('Apakah Anda yakin data hosting sudah sesuai dan ingin mengaktifkan aplikasi ini menjadi LIVE?')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Simpan & Jadikan Aplikasi LIVE</span>
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
