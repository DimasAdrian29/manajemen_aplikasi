@extends('layouts.persandian')

@section('title', 'Riwayat & Arsip Audit Keamanan')
@section('page_title', 'Arsip Riwayat Pengujian Keamanan')
@section('page_subtitle', 'Katalog seluruh hasil evaluasi kerentanan, vulnerability assessment, penetration testing, dan sertifikasi kelaikan operasi oleh CSIRT Riau')

@section('content')
<div class="space-y-6">

    <!-- Filter & Search Bar -->
    <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('persandian.pengujian.riwayat') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="md:col-span-2 relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor SKLO, nama proyek, aplikasi, instansi, atau temuan..." class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                </div>

                <!-- Filter Hasil -->
                <div>
                    <select name="hasil" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="">Semua Hasil Audit</option>
                        <option value="lulus" {{ request('hasil') === 'lulus' ? 'selected' : '' }}>Lulus (Laik Operasi)</option>
                        <option value="perbaikan" {{ request('hasil') === 'perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="tidak_lulus" {{ request('hasil') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>

                <!-- Filter Tingkat Risiko -->
                <div>
                    <select name="tingkat_risiko" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="">Semua Tingkat Risiko</option>
                        <option value="aman" {{ request('tingkat_risiko') === 'aman' ? 'selected' : '' }}>Aman (Clean)</option>
                        <option value="rendah" {{ request('tingkat_risiko') === 'rendah' ? 'selected' : '' }}>Rendah (Low)</option>
                        <option value="sedang" {{ request('tingkat_risiko') === 'sedang' ? 'selected' : '' }}>Sedang (Medium)</option>
                        <option value="tinggi" {{ request('tingkat_risiko') === 'tinggi' ? 'selected' : '' }}>Tinggi (High)</option>
                        <option value="kritis" {{ request('tingkat_risiko') === 'kritis' ? 'selected' : '' }}>Kritis (Critical)</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1 border-t border-slate-100">
                <div class="w-full sm:w-auto">
                    <select name="kategori_pengujian" class="w-full sm:w-64 px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#0D47A1] focus:bg-white transition">
                        <option value="">Semua Metode Pengujian</option>
                        <option value="penetration_testing" {{ request('kategori_pengujian') === 'penetration_testing' ? 'selected' : '' }}>Penetration Testing (Pentest)</option>
                        <option value="vulnerability_assessment" {{ request('kategori_pengujian') === 'vulnerability_assessment' ? 'selected' : '' }}>Vulnerability Assessment (VA)</option>
                        <option value="source_code_review" {{ request('kategori_pengujian') === 'source_code_review' ? 'selected' : '' }}>Source Code Security Review</option>
                        <option value="audit_konfigurasi" {{ request('kategori_pengujian') === 'audit_konfigurasi' ? 'selected' : '' }}>Audit Konfigurasi & Server</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    @if (request()->hasAny(['search', 'hasil', 'tingkat_risiko', 'kategori_pengujian']))
                        <a href="{{ route('persandian.pengujian.riwayat') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="px-5 py-2 bg-[#0D47A1] text-white text-xs font-bold rounded-xl hover:bg-[#1565C0] transition flex items-center justify-center gap-1.5 shadow-xs">
                        <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Audit Records Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Tgl & No. SKLO</th>
                        <th class="px-6 py-4">Proyek & Instansi</th>
                        <th class="px-6 py-4">Metode Audit</th>
                        <th class="px-6 py-4 text-center">Tingkat Risiko</th>
                        <th class="px-6 py-4 text-center">Hasil Audit</th>
                        <th class="px-6 py-4">Auditor CSIRT</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($audits as $audit)
                        @php
                            $riskColor = match($audit->tingkat_risiko) {
                                'kritis' => 'bg-rose-100 text-rose-900 border-rose-300',
                                'tinggi' => 'bg-amber-100 text-amber-900 border-amber-300',
                                'sedang' => 'bg-yellow-100 text-yellow-900 border-yellow-300',
                                'rendah' => 'bg-blue-100 text-blue-900 border-blue-300',
                                default => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                            };

                            $kategoriLabel = match($audit->kategori_pengujian) {
                                'penetration_testing' => 'Penetration Testing',
                                'vulnerability_assessment' => 'Vulnerability Assessment',
                                'source_code_review' => 'Source Code Review',
                                'audit_konfigurasi' => 'Audit Konfigurasi',
                                default => ucwords(str_replace('_', ' ', $audit->kategori_pengujian ?? 'Audit Keamanan')),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $audit->tanggal_uji?->format('d/m/Y') ?? '-' }}</div>
                                @if ($audit->nomor_surat)
                                    <div class="text-[11px] font-mono text-slate-500 mt-0.5 truncate max-w-[150px]" title="{{ $audit->nomor_surat }}">
                                        {{ $audit->nomor_surat }}
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 italic mt-0.5">Tanpa SKLO</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $audit->project?->nama_project ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $audit->project?->aplikasi?->instansi?->nama_instansi ?? 'Pemerintah Provinsi Riau' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium text-[11px]">
                                    <span class="material-symbols-outlined text-[14px]">terminal</span>
                                    <span>{{ $kategoriLabel }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $riskColor }} uppercase inline-block">
                                    {{ $audit->tingkat_risiko ?? 'Aman' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($audit->hasil === 'lulus')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Lulus
                                    </span>
                                @elseif ($audit->hasil === 'perbaikan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        Perbaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Tidak Lulus
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $audit->tester?->name ?? 'Auditor CSIRT' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $audit->tester?->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('persandian.pengujian.show', $audit) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition" title="Lihat Lembar Hasil Audit">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        <span>Detail</span>
                                    </a>
                                    @if ($audit->file_hasil)
                                        <a href="{{ asset('storage/' . $audit->file_hasil) }}" target="_blank" class="p-1.5 rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition" title="Unduh Berkas Laporan">
                                            <span class="material-symbols-outlined text-[16px]">download</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                    <span class="material-symbols-outlined text-[24px]">shield</span>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">Belum ada riwayat audit keamanan yang tercatat.</p>
                                <p class="text-xs text-slate-400 mt-1">Audit yang dilakukan melalui antrean pengujian akan otomatis diarsipkan di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($audits->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $audits->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
