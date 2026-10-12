@extends('layouts.infrastruktur')

@section('title', 'Inventaris Server & Hosting')
@section('page_title', 'Inventaris Server & Hosting')
@section('page_subtitle', 'Katalog seluruh alokasi domain, spesifikasi server komputasi, dan sertifikat keamanan aplikasi Pemerintah Provinsi Riau')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        <form method="GET" action="{{ route('infrastruktur.hosting.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="relative lg:col-span-2">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari domain, IP, nama server, aplikasi, instansi..." class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <div>
                    <select name="jenis_hosting" class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="">Semua Jenis Hosting</option>
                        <option value="vps" {{ request('jenis_hosting') === 'vps' ? 'selected' : '' }}>VPS</option>
                        <option value="container" {{ request('jenis_hosting') === 'container' ? 'selected' : '' }}>Container (Docker/K8s)</option>
                        <option value="cloud" {{ request('jenis_hosting') === 'cloud' ? 'selected' : '' }}>Cloud</option>
                        <option value="on_premise" {{ request('jenis_hosting') === 'on_premise' ? 'selected' : '' }}>On-Premise</option>
                        <option value="shared" {{ request('jenis_hosting') === 'shared' ? 'selected' : '' }}>Shared</option>
                    </select>
                </div>

                <div>
                    <select name="status_hosting" class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status_hosting') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="persiapan" {{ request('status_hosting') === 'persiapan' ? 'selected' : '' }}>Persiapan</option>
                        <option value="maintenance" {{ request('status_hosting') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="nonaktif" {{ request('status_hosting') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <select name="ssl" class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="">Semua Status SSL</option>
                        <option value="aktif" {{ request('ssl') === 'aktif' ? 'selected' : '' }}>SSL Aktif</option>
                        <option value="expiring" {{ request('ssl') === 'expiring' ? 'selected' : '' }}>SSL Expiring (&le;30h)</option>
                        <option value="nonaktif" {{ request('ssl') === 'nonaktif' ? 'selected' : '' }}>SSL Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-slate-100">
                @if (request()->hasAny(['search', 'jenis_hosting', 'status_hosting', 'ssl']))
                    <a href="{{ route('infrastruktur.hosting.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        Reset Filter
                    </a>
                @endif
                <button type="submit" class="px-4 py-2 bg-[#0D47A1] text-white text-xs font-bold rounded-xl hover:bg-[#1565C0] transition flex items-center gap-1.5 shadow-xs">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Table of Hosting Records -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Domain & URL</th>
                        <th class="px-6 py-4">Aplikasi & Instansi</th>
                        <th class="px-6 py-4">Server & Komputasi</th>
                        <th class="px-6 py-4">Sertifikat SSL</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($hostings as $item)
                        @php
                            $isExpiring = $item->ssl_aktif && $item->ssl_kadaluarsa && now()->diffInDays($item->ssl_kadaluarsa, false) <= 30;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                    {{ $item->nama_domain ?? '-' }}
                                    @if ($item->url_aplikasi)
                                        <a href="{{ $item->url_aplikasi }}" target="_blank" class="text-slate-400 hover:text-[#0D47A1] transition" title="Buka tautan">
                                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                        </a>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 truncate max-w-xs">
                                    {{ $item->url_aplikasi ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $item->project?->aplikasi?->nama_aplikasi ?? $item->project?->nama_project ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $item->project?->aplikasi?->instansi?->nama_instansi ?? 'Pemerintah Provinsi Riau' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-slate-800 font-semibold">{{ $item->ip_server ?? 'IP: -' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ strtoupper($item->jenis_hosting ?? 'vps') }}
                                    @if ($item->cpu_core) &bull; {{ $item->cpu_core }}C/{{ $item->ram_gb }}GB @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->ssl_aktif)
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] {{ $isExpiring ? 'text-amber-500' : 'text-emerald-500' }}">lock</span>
                                        <span class="font-semibold {{ $isExpiring ? 'text-amber-700' : 'text-emerald-700' }}">Aktif</span>
                                    </div>
                                    @if ($item->ssl_kadaluarsa)
                                        <div class="text-[10px] {{ $isExpiring ? 'text-amber-600 font-bold' : 'text-slate-400' }} mt-0.5">
                                            Exp: {{ $item->ssl_kadaluarsa->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-400">
                                        <span class="material-symbols-outlined text-[16px]">lock_open</span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->status_hosting === 'aktif')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @elseif ($item->status_hosting === 'maintenance')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Maintenance
                                    </span>
                                @elseif ($item->status_hosting === 'persiapan')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Persiapan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('infrastruktur.hosting.show', $item) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0D47A1] hover:bg-blue-50 transition" title="Lihat Detail & Riwayat">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a href="{{ route('infrastruktur.hosting.edit', $item) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Hosting & Catat Riwayat">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-[28px]">dns</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">Tidak Ada Data Hosting</h4>
                                <p class="text-xs text-slate-400 mt-1">Belum ada data hosting yang cocok dengan pencarian filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($hostings->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $hostings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
