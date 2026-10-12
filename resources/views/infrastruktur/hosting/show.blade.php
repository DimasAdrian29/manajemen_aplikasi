@extends('layouts.infrastruktur')

@section('title', 'Detail Hosting - ' . ($hosting->nama_domain ?? 'Hosting'))
@section('page_title', 'Detail Hosting & Riwayat Perubahan')
@section('page_subtitle', 'Informasi spesifikasi server komputasi, sertifikat keamanan, dan audit jejak pembaruan infrastruktur')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('infrastruktur.hosting.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Inventaris Server</span>
        </a>
        <div class="flex items-center gap-2">
            @if ($hosting->url_aplikasi)
                <a href="{{ $hosting->url_aplikasi }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    <span>Buka Aplikasi</span>
                </a>
            @endif
            <a href="{{ route('infrastruktur.hosting.edit', $hosting) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] transition shadow-xs">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                <span>Edit & Perbarui Hosting</span>
            </a>
        </div>
    </div>

    <!-- Main Overview Header Card -->
    <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0 border border-blue-100">
                    <span class="material-symbols-outlined text-[32px]">dns</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-bold text-slate-900">{{ $hosting->nama_domain ?? 'Domain Belum Diisi' }}</h2>
                        @if ($hosting->status_hosting === 'aktif')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif (Live)
                            </span>
                        @elseif ($hosting->status_hosting === 'maintenance')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Maintenance
                            </span>
                        @elseif ($hosting->status_hosting === 'persiapan')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                Persiapan
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1 font-mono">
                        {{ $hosting->url_aplikasi ?? '-' }}
                    </p>
                    <p class="text-xs text-slate-600 mt-2">
                        Aplikasi: <strong class="text-slate-800">{{ $hosting->project?->aplikasi?->nama_aplikasi ?? $hosting->project?->nama_project ?? '-' }}</strong> &bull;
                        Instansi: <span class="text-slate-700">{{ $hosting->project?->aplikasi?->instansi?->nama_instansi ?? 'Pemerintah Provinsi Riau' }}</span>
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex md:flex-col items-end gap-1 text-xs">
                <span class="text-slate-400 text-[11px]">Tanggal Go-Live</span>
                <span class="font-bold text-slate-800">{{ $hosting->tanggal_live?->format('d F Y') ?? '-' }}</span>
                <span class="text-slate-400 text-[11px] mt-1">Dikelola Oleh</span>
                <span class="font-medium text-slate-700">{{ $hosting->adminInfrastruktur?->name ?? 'Bidang TIK' }}</span>
            </div>
        </div>
    </div>

    <!-- Specifications Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Server & Komputasi -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-[#0D47A1]">memory</span>
                <h3 class="text-sm font-bold text-slate-900">Spesifikasi Komputasi & Server</h3>
            </div>

            <dl class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Jenis Hosting</dt>
                    <dd class="font-bold text-slate-800 uppercase">{{ $hosting->jenis_hosting ?? 'vps' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">IP Address Server</dt>
                    <dd class="font-mono font-bold text-slate-800">{{ $hosting->ip_server ?? '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Hostname / Server VM</dt>
                    <dd class="font-medium text-slate-800">{{ $hosting->nama_server ?? '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Sistem Operasi</dt>
                    <dd class="font-medium text-slate-800">{{ $hosting->sistem_operasi ?? '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Alokasi CPU</dt>
                    <dd class="font-bold text-slate-800">{{ $hosting->cpu_core ? $hosting->cpu_core . ' Core' : '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Alokasi Memory RAM</dt>
                    <dd class="font-bold text-cyan-700">{{ $hosting->ram_gb ? $hosting->ram_gb . ' GB' : '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Penyimpanan Harddisk</dt>
                    <dd class="font-bold text-emerald-700">{{ $hosting->storage_gb ? $hosting->storage_gb . ' GB' : '-' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Software Stack & Keamanan SSL -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-emerald-600">security</span>
                <h3 class="text-sm font-bold text-slate-900">Perangkat Lunak & Keamanan SSL</h3>
            </div>

            <dl class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Web Server Engine</dt>
                    <dd class="font-medium text-slate-800">{{ $hosting->web_server ?? '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Database Engine</dt>
                    <dd class="font-medium text-slate-800">{{ $hosting->database_server ?? '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Status Sertifikat SSL</dt>
                    <dd>
                        @if ($hosting->ssl_aktif)
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 text-[11px]">
                                <span class="material-symbols-outlined text-[14px]">lock</span>
                                Terenkripsi (HTTPS)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded-full text-[11px]">
                                <span class="material-symbols-outlined text-[14px]">lock_open</span>
                                Belum Terpasang
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <dt class="text-slate-500">Masa Berlaku SSL</dt>
                    <dd class="font-medium text-slate-800">
                        {{ $hosting->ssl_kadaluarsa?->format('d F Y') ?? '-' }}
                    </dd>
                </div>
                @if ($hosting->ssl_aktif && $hosting->ssl_kadaluarsa)
                    @php
                        $daysLeft = now()->diffInDays($hosting->ssl_kadaluarsa, false);
                    @endphp
                    <div class="py-2.5 flex items-center justify-between">
                        <dt class="text-slate-500">Sisa Masa Aktif SSL</dt>
                        <dd class="font-bold {{ $daysLeft <= 30 ? 'text-rose-600' : 'text-emerald-700' }}">
                            {{ $daysLeft <= 0 ? 'Kadaluarsa Hari Ini' : $daysLeft . ' Hari Lagi' }}
                        </dd>
                    </div>
                @endif
                <div class="py-2.5">
                    <dt class="text-slate-500 mb-1">Keterangan / Catatan Operasional</dt>
                    <dd class="p-3 rounded-xl bg-slate-50 text-slate-700 whitespace-pre-line text-[11px] leading-relaxed">
                        {{ $hosting->keterangan ?: 'Tidak ada catatan tambahan.' }}
                    </dd>
                </div>
            </dl>
        </div>

    </div>

    <!-- Riwayat Perubahan Hosting (Audit Trail) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">history</span>
                <h3 class="text-sm font-bold text-slate-900">Riwayat Perubahan & Audit Jejak Server</h3>
            </div>
            <span class="text-xs text-slate-400 font-medium">{{ $hosting->riwayat->count() }} Perubahan Tercatat</span>
        </div>

        @if ($hosting->riwayat->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                <span class="material-symbols-outlined text-slate-300 text-[32px] block mb-2">history_edu</span>
                Belum ada catatan perubahan konfigurasi untuk server ini. Data masih menggunakan konfigurasi awal deployment.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($hosting->riwayat as $history)
                    <div class="p-5 hover:bg-slate-50/50 transition">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900">{{ $history->user?->name ?? 'Admin TIK' }}</span>
                                    <span class="text-[10px] text-slate-400">&bull; {{ $history->created_at->format('d M Y, H:i') }} WIB</span>
                                </div>
                                <div class="mt-2 p-3 rounded-xl bg-purple-50/50 border border-purple-100 text-xs text-purple-950 font-medium leading-relaxed">
                                    <strong class="text-purple-900 block text-[11px] uppercase tracking-wider mb-0.5">Alasan Perubahan:</strong>
                                    {{ $history->alasan_perubahan }}
                                </div>
                            </div>
                        </div>

                        <!-- Data Snapshot Sebelum -->
                        <div class="mt-3">
                            <details class="group">
                                <summary class="cursor-pointer text-[11px] font-semibold text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 select-none">
                                    <span class="material-symbols-outlined text-[14px] group-open:rotate-90 transition">chevron_right</span>
                                    <span>Lihat Snapshot Konfigurasi Sebelum Diubah</span>
                                </summary>
                                <div class="mt-2 p-3 rounded-xl bg-slate-900 text-slate-200 text-[11px] font-mono overflow-x-auto">
                                    <pre>{{ json_encode($history->data_sebelum, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                </div>
                            </details>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
