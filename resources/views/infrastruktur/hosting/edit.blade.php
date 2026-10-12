@extends('layouts.infrastruktur')

@section('title', 'Edit Hosting - ' . ($hosting->nama_domain ?? 'Hosting'))
@section('page_title', 'Perbarui Informasi Hosting & Server')
@section('page_subtitle', 'Ubah spesifikasi, domain, atau status server dengan mencatat alasan perubahan ke dalam jejak audit')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('infrastruktur.hosting.show', $hosting) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Detail Hosting</span>
        </a>
        <span class="text-xs text-slate-400">
            Aplikasi: <strong class="text-slate-700">{{ $hosting->project?->aplikasi?->nama_aplikasi ?? $hosting->project?->nama_project ?? '-' }}</strong>
        </span>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('infrastruktur.hosting.update', $hosting) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Audit Reason Warning Banner -->
        <div class="p-6 bg-purple-50/70 border border-purple-200 rounded-2xl shadow-xs space-y-2">
            <div class="flex items-center gap-2 text-purple-900 font-bold text-sm">
                <span class="material-symbols-outlined text-[20px] text-purple-700">history_edu</span>
                <h3>Audit Jejak Perubahan (Wajib Diisi)</h3>
            </div>
            <p class="text-xs text-purple-800/80 leading-relaxed">
                Setiap perubahan pada spesifikasi server, domain, atau status operasional akan disimpan sebagai snapshot riwayat untuk transparansi dan kepatuhan audit sistem TIK.
            </p>
            <div class="pt-2">
                <label class="block text-xs font-bold text-purple-950 mb-1">
                    Alasan Perubahan Spesifikasi / Hosting <span class="text-rose-500">*</span>
                </label>
                <textarea name="alasan_perubahan" rows="3" required placeholder="Contoh: Upgrade kapasitas RAM dari 4GB ke 8GB karena lonjakan trafik pendaftaran, atau perpanjangan sertifikat SSL..." class="w-full px-3.5 py-2.5 text-xs bg-white border border-purple-200 rounded-xl focus:outline-none focus:border-purple-600 transition">{{ old('alasan_perubahan') }}</textarea>
                <p class="text-[11px] text-purple-700 mt-1">Minimal 5 karakter.</p>
            </div>
        </div>

        <!-- Section 1: Domain & URL Publik -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-[#0D47A1]">language</span>
                <h3 class="text-sm font-bold text-slate-900">1. Konfigurasi Domain & URL</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Domain <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_domain" value="{{ old('nama_domain', $hosting->nama_domain) }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        URL Aplikasi Publik <span class="text-rose-500">*</span>
                    </label>
                    <input type="url" name="url_aplikasi" value="{{ old('url_aplikasi', $hosting->url_aplikasi) }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Status Hosting <span class="text-rose-500">*</span>
                    </label>
                    <select name="status_hosting" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="aktif" {{ old('status_hosting', $hosting->status_hosting) === 'aktif' ? 'selected' : '' }}>Aktif (Live Publik)</option>
                        <option value="persiapan" {{ old('status_hosting', $hosting->status_hosting) === 'persiapan' ? 'selected' : '' }}>Persiapan (Staging)</option>
                        <option value="maintenance" {{ old('status_hosting', $hosting->status_hosting) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="nonaktif" {{ old('status_hosting', $hosting->status_hosting) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tanggal Go-Live
                    </label>
                    <input type="date" name="tanggal_live" value="{{ old('tanggal_live', $hosting->tanggal_live?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>
        </div>

        <!-- Section 2: Server & Hardware -->
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
                        <option value="vps" {{ old('jenis_hosting', $hosting->jenis_hosting) === 'vps' ? 'selected' : '' }}>VPS (Virtual Private Server)</option>
                        <option value="container" {{ old('jenis_hosting', $hosting->jenis_hosting) === 'container' ? 'selected' : '' }}>Container (Docker / K8s)</option>
                        <option value="cloud" {{ old('jenis_hosting', $hosting->jenis_hosting) === 'cloud' ? 'selected' : '' }}>Cloud Server (IaaS)</option>
                        <option value="on_premise" {{ old('jenis_hosting', $hosting->jenis_hosting) === 'on_premise' ? 'selected' : '' }}>On-Premise (Data Center Riau)</option>
                        <option value="shared" {{ old('jenis_hosting', $hosting->jenis_hosting) === 'shared' ? 'selected' : '' }}>Shared Hosting</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        IP Address Server
                    </label>
                    <input type="text" name="ip_server" value="{{ old('ip_server', $hosting->ip_server) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Host / Hostname Server
                    </label>
                    <input type="text" name="nama_server" value="{{ old('nama_server', $hosting->nama_server) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Sistem Operasi
                    </label>
                    <input type="text" name="sistem_operasi" value="{{ old('sistem_operasi', $hosting->sistem_operasi) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        CPU (Core)
                    </label>
                    <input type="number" name="cpu_core" min="1" max="256" value="{{ old('cpu_core', $hosting->cpu_core) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        RAM (GB)
                    </label>
                    <input type="number" name="ram_gb" min="1" max="1024" value="{{ old('ram_gb', $hosting->ram_gb) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Storage Disk (GB)
                    </label>
                    <input type="number" name="storage_gb" min="1" max="65535" value="{{ old('storage_gb', $hosting->storage_gb) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Web Server Stack
                    </label>
                    <input type="text" name="web_server" value="{{ old('web_server', $hosting->web_server) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Database Engine
                    </label>
                    <input type="text" name="database_server" value="{{ old('database_server', $hosting->database_server) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>
            </div>
        </div>

        <!-- Section 3: SSL -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-emerald-600">lock</span>
                <h3 class="text-sm font-bold text-slate-900">3. Sertifikat SSL / HTTPS</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="ssl_aktif" value="1" {{ old('ssl_aktif', $hosting->ssl_aktif) ? 'checked' : '' }} class="w-4 h-4 text-[#0D47A1] rounded border-slate-300 focus:ring-0">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Sertifikat SSL Aktif (HTTPS)</span>
                            <span class="text-[11px] text-slate-500 block">Enkripsi TLS/SSL terpasang dan valid</span>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Masa Berlaku SSL Sampai
                    </label>
                    <input type="date" name="ssl_kadaluarsa" value="{{ old('ssl_kadaluarsa', $hosting->ssl_kadaluarsa?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
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
                        Spesifikasi Hosting Lainnya
                    </label>
                    <textarea name="spesifikasi_hosting" rows="3" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">{{ old('spesifikasi_hosting', $hosting->spesifikasi_hosting) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Keterangan Tambahan
                    </label>
                    <textarea name="keterangan" rows="3" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">{{ old('keterangan', $hosting->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-4">
            <a href="{{ route('infrastruktur.hosting.show', $hosting) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] shadow-sm transition">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Simpan Perubahan & Rekam Riwayat</span>
            </button>
        </div>
    </form>

</div>
@endsection
