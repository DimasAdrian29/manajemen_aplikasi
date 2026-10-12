@extends('layouts.eksekutif')

@section('title', 'Laporan Eksekutif Portofolio SPBE')
@section('page-title', 'Laporan Ringkasan Eksekutif (Executive Summary)')
@section('breadcrumb', 'Laporan Eksekutif')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Action Bar for Printing (Hidden when printed) -->
    <div class="flex items-center justify-between no-print bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Format Laporan Siap Cetak (Executive Summary)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Dokumen rekapitulasi portofolio aplikasi daerah untuk laporan pimpinan daerah dan rapat evaluasi SPBE.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold transition flex items-center gap-2 shadow-sm shadow-[#0D47A1]/20 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Cetak / Unduh PDF</span>
        </button>
    </div>

    <!-- Formal Printable Document Sheet -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-12 shadow-sm print:shadow-none print:border-none print:p-0 space-y-8">

        <!-- Kop Surat Resmi Pemprov Riau -->
        <div class="flex items-center gap-5 pb-6 border-b-2 border-slate-900">
            <div class="w-20 h-20 shrink-0 flex items-center justify-center">
                <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Kominfo" class="w-full h-full object-contain">
            </div>
            <div class="text-center flex-1">
                <h3 class="text-base sm:text-lg font-bold uppercase tracking-wide text-slate-900">Pemerintah Provinsi Riau</h3>
                <h2 class="text-lg sm:text-xl font-extrabold uppercase tracking-wider text-slate-950 mt-0.5">Dinas Komunikasi, Informatika dan Statistik</h2>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Jl. Jenderal Sudirman No. 460, Pekanbaru, Riau &bull; Telp: (0761) 23456 &bull; Website: diskominfotik.riau.go.id
                </p>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center space-y-1">
            <h3 class="text-base sm:text-lg font-black uppercase text-slate-900 tracking-tight">
                Laporan Ringkasan Eksekutif Portofolio Sistem Informasi & SPBE Daerah
            </h3>
            <p class="text-xs text-slate-500 font-mono">
                Periode Pelaporan: {{ date('d F Y') }} &bull; Sumber Data: SIMBANGDA Riau
            </p>
        </div>

        <!-- Bagian 1: Metrik Makro Kinerja SPBE -->
        <div class="space-y-3">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-1.5 flex items-center gap-2">
                <span>I.</span>
                <span>Ringkasan Makro Portofolio Aplikasi & Proyek Daerah</span>
            </h4>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Total Aplikasi Terdata</span>
                    <span class="text-lg font-black text-slate-900 mt-0.5 block">{{ $totalAplikasi }} Sistem</span>
                </div>
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Aplikasi Aktif (Live)</span>
                    <span class="text-lg font-black text-emerald-700 mt-0.5 block">{{ $aplikasiAktif }} Sistem</span>
                </div>
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Tahap Pengembangan</span>
                    <span class="text-lg font-black text-amber-700 mt-0.5 block">{{ $aplikasiPengembangan }} Proyek</span>
                </div>
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Aplikasi Tidak Aktif</span>
                    <span class="text-lg font-black text-slate-600 mt-0.5 block">{{ $aplikasiNonaktif }} Sistem</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs mt-2">
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Proyek Berjalan</span>
                    <span class="text-lg font-black text-[#0D47A1] mt-0.5 block">{{ $projectAktif }} Proyek</span>
                </div>
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Proyek Tertunda (Kendala)</span>
                    <span class="text-lg font-black {{ $projectDelayed > 0 ? 'text-rose-700' : 'text-slate-900' }} mt-0.5 block">{{ $projectDelayed }} Proyek</span>
                </div>
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Audit Siber CSIRT</span>
                    <span class="text-lg font-black text-purple-700 mt-0.5 block">{{ $totalAuditCsirt }} Audit</span>
                </div>
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="text-slate-500 block text-[11px]">Server Hosting Aktif</span>
                    <span class="text-lg font-black text-blue-700 mt-0.5 block">{{ $hostingAktif }} Server</span>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Rekapitulasi Aplikasi per Perangkat Daerah (OPD) -->
        <div class="space-y-3">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-1.5 flex items-center gap-2">
                <span>II.</span>
                <span>Distribusi Kepemilikan Aplikasi Berdasarkan Perangkat Daerah (OPD)</span>
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse border border-slate-300 text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold">
                            <th class="py-2 px-3 border border-slate-300 w-10 text-center">No</th>
                            <th class="py-2 px-3 border border-slate-300">Nama Perangkat Daerah (Instansi)</th>
                            <th class="py-2 px-3 border border-slate-300 text-center w-24">Aktif</th>
                            <th class="py-2 px-3 border border-slate-300 text-center w-28">Pengembangan</th>
                            <th class="py-2 px-3 border border-slate-300 text-center w-24">Nonaktif</th>
                            <th class="py-2 px-3 border border-slate-300 text-center w-24">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapOpd as $idx => $opd)
                            <tr>
                                <td class="py-2 px-3 border border-slate-300 text-center">{{ $idx + 1 }}</td>
                                <td class="py-2 px-3 border border-slate-300 font-semibold">{{ $opd->nama_instansi }}</td>
                                <td class="py-2 px-3 border border-slate-300 text-center">{{ $opd->aplikasi_aktif }}</td>
                                <td class="py-2 px-3 border border-slate-300 text-center">{{ $opd->aplikasi_pengembangan }}</td>
                                <td class="py-2 px-3 border border-slate-300 text-center">{{ $opd->aplikasi_nonaktif }}</td>
                                <td class="py-2 px-3 border border-slate-300 text-center font-bold">{{ $opd->total_aplikasi }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-slate-400">Belum ada data OPD yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bagian 3: Status Seluruh Proyek Pembangunan Daerah -->
        <div class="space-y-3">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-1.5 flex items-center gap-2">
                <span>III.</span>
                <span>Lampiran Status Proyek Pembangunan & Capaian Progres</span>
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse border border-slate-300 text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold">
                            <th class="py-2 px-3 border border-slate-300 w-10 text-center">No</th>
                            <th class="py-2 px-3 border border-slate-300">Nama Proyek</th>
                            <th class="py-2 px-3 border border-slate-300">Perangkat Daerah</th>
                            <th class="py-2 px-3 border border-slate-300 text-center w-20">Progres</th>
                            <th class="py-2 px-3 border border-slate-300 text-center w-32">Status Operasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $idx => $p)
                            <tr>
                                <td class="py-2 px-3 border border-slate-300 text-center">{{ $idx + 1 }}</td>
                                <td class="py-2 px-3 border border-slate-300 font-bold">{{ $p->nama_project }}</td>
                                <td class="py-2 px-3 border border-slate-300">{{ $p->aplikasi->instansi->singkatan ?? ($p->aplikasi->instansi->nama_instansi ?? '-') }}</td>
                                <td class="py-2 px-3 border border-slate-300 text-center font-bold">{{ $p->progres_total }}%</td>
                                <td class="py-2 px-3 border border-slate-300 text-center font-mono uppercase text-[11px]">
                                    {{ str_replace('_', ' ', $p->status) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-400">Belum ada proyek yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bagian Tanda Tangan Pimpinan Eksekutif -->
        <div class="pt-8 flex justify-end">
            <div class="text-center w-72 text-xs space-y-1">
                <div>Pekanbaru, {{ date('d F Y') }}</div>
                <div class="font-bold">Kepala Dinas Komunikasi, Informatika dan Statistik Provinsi Riau</div>
                <div class="h-20"></div>
                <div class="font-bold underline text-slate-900">Dr. H. Ikhwan Ridwan, S.H., M.Si.</div>
                <div class="text-slate-500">Pembina Utama Madya / NIP. 19680512 199303 1 004</div>
            </div>
        </div>

    </div>

</div>
@endsection
