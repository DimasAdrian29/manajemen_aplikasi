@extends('layouts.infrastruktur')

@section('title', 'Antrean Penonaktifan Aplikasi')
@section('page_title', 'Antrean Penonaktifan & Terminasi Server')
@section('page_subtitle', 'Pengajuan penonaktifan aplikasi dari OPD yang telah disetujui Bidang APTIKA, menunggu eksekusi penutupan domain dan alokasi server')

@section('content')
<div class="space-y-6">

    <!-- Explanatory Banner -->
    <div class="p-5 rounded-2xl bg-purple-50/70 border border-purple-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">power_settings_new</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-purple-900">Terminasi Layanan & Pelepasan Resource</h3>
                <p class="text-xs text-purple-800/80 mt-0.5 leading-relaxed">
                    Aplikasi dalam antrean ini telah diverifikasi untuk dinonaktifkan. Menyetujui eksekusi akan mengubah status aplikasi menjadi <strong>Nonaktif</strong>, menonaktifkan DNS domain & server terkait, dan melepaskan resource server komputasi.
                </p>
            </div>
        </div>
    </div>

    <!-- Active Queue Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">pending_actions</span>
                <h2 class="text-sm font-bold text-slate-900">Antrean Menunggu Eksekusi Penonaktifan</h2>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                {{ $pengajuans->total() }} Permohonan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Aplikasi & Instansi</th>
                        <th class="px-6 py-4">Alasan & Persetujuan APTIKA</th>
                        <th class="px-6 py-4">Infrastruktur Saat Ini</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengajuans as $pengajuan)
                        @php
                            $project = $pengajuan->aplikasi?->projects?->last();
                            $hosting = $project?->hostingAplikasi;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $pengajuan->nama_aplikasi }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $pengajuan->instansi?->nama_instansi ?? '-' }} &bull;
                                    <span class="font-mono text-slate-400">{{ $pengajuan->kode_pengajuan }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-sm">
                                <div class="text-xs text-slate-800 line-clamp-2">
                                    {{ $pengajuan->deskripsi_kebutuhan ?: 'Permohonan penonaktifan aplikasi.' }}
                                </div>
                                <div class="text-[10px] text-emerald-700 font-semibold mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Disetujui: {{ $pengajuan->penilai?->name ?? 'Admin APTIKA' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($hosting)
                                    <div class="font-semibold text-slate-800 font-mono">{{ $hosting->nama_domain ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        IP: <span class="font-mono">{{ $hosting->ip_server ?? '-' }}</span> &bull;
                                        <span>{{ strtoupper($hosting->jenis_hosting ?? 'VPS') }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Hosting tidak terdata</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <!-- Trigger Modal / Details Form -->
                                <button type="button" onclick="document.getElementById('deactivate-modal-{{ $pengajuan->id }}').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-xs transition">
                                    <span class="material-symbols-outlined text-[16px]">power_settings_new</span>
                                    <span>Tutup Server & Nonaktifkan</span>
                                </button>

                                <!-- Modal Konfirmasi Penonaktifan -->
                                <div id="deactivate-modal-{{ $pengajuan->id }}" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
                                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 text-left shadow-xl border border-slate-200">
                                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                            <div class="flex items-center gap-2 text-rose-600 font-bold text-sm">
                                                <span class="material-symbols-outlined text-[20px]">warning</span>
                                                <h3>Konfirmasi Penonaktifan Aplikasi</h3>
                                            </div>
                                            <button type="button" onclick="document.getElementById('deactivate-modal-{{ $pengajuan->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                                                <span class="material-symbols-outlined text-[20px]">close</span>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('infrastruktur.penonaktifan.execute', $pengajuan) }}" class="mt-4 space-y-4">
                                            @csrf
                                            <p class="text-xs text-slate-600 leading-relaxed">
                                                Apakah Anda yakin ingin mengeksekusi penonaktifan aplikasi <strong>{{ $pengajuan->nama_aplikasi }}</strong>?
                                                Status hosting akan diubah menjadi <strong class="text-rose-600">Nonaktif</strong> dan alokasi server akan dilepas.
                                            </p>

                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Catatan Teknis Penonaktifan (Opsional)
                                                </label>
                                                <textarea name="catatan" rows="3" placeholder="Contoh: Backup database telah dibuat di storage arsip, domain dinonaktifkan dari DNS cloudflare..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-600 transition"></textarea>
                                            </div>

                                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                                <button type="button" onclick="document.getElementById('deactivate-modal-{{ $pengajuan->id }}').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100 transition">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition">
                                                    Konfirmasi Nonaktifkan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-[28px]">check_circle</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">Tidak Ada Antrean Penonaktifan</h4>
                                <p class="text-xs text-slate-400 mt-1">Seluruh pengajuan penonaktifan telah selesai diproses.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengajuans->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>

    <!-- Riwayat Aplikasi Selesai Dinonaktifkan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-500">archive</span>
                <h3 class="text-sm font-bold text-slate-900">Riwayat Penonaktifan Aplikasi Selesai</h3>
            </div>
        </div>

        @if ($decommissioned->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                Belum ada aplikasi yang dinonaktifkan melalui sistem ini.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($decommissioned as $done)
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-900">{{ $done->nama_aplikasi }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Nonaktif
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Instansi: {{ $done->instansi?->nama_instansi ?? '-' }} &bull;
                                Kode: <span class="font-mono">{{ $done->kode_pengajuan }}</span>
                            </p>
                            @if ($done->catatan_penilaian)
                                <p class="text-[11px] text-slate-400 mt-1 italic">
                                    {{ Str::limit($done->catatan_penilaian, 120) }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[11px] text-slate-400 block">Selesai Ditutup</span>
                            <span class="text-xs font-semibold text-slate-700">{{ $done->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
