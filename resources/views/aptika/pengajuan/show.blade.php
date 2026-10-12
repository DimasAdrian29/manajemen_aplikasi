@extends('layouts.aptika')

@section('title', 'Telaah Usulan ' . $pengajuan->kode_pengajuan)
@section('page-title', 'Lembar Telaah Usulan OPD')
@section('breadcrumb', 'Telaah Pengajuan / ' . $pengajuan->kode_pengajuan)

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="font-mono text-sm font-extrabold text-[#0D47A1] bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                    {{ $pengajuan->kode_pengajuan }}
                </span>
                @if ($pengajuan->jenis_pengajuan === 'new_app')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Aplikasi Baru
                    </span>
                @elseif ($pengajuan->jenis_pengajuan === 'app_development')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        Pengembangan
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        Penonaktifan
                    </span>
                @endif

                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold 
                    @if ($pengajuan->status === 'submitted') bg-blue-50 text-blue-700 border border-blue-200
                    @elseif ($pengajuan->status === 'under_review') bg-amber-50 text-amber-700 border border-amber-200
                    @elseif ($pengajuan->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @else bg-rose-50 text-rose-700 border border-rose-200
                    @endif">
                    Status: {{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                </span>
            </div>

            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                {{ $pengajuan->nama_aplikasi ?? ($pengajuan->aplikasi->nama_aplikasi ?? 'Usulan Sistem') }}
            </h2>
            <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                <span>Instansi: <strong>{{ $pengajuan->instansi->nama_instansi }}</strong></span>
                <span>•</span>
                <span>Tanggal Kirim: <strong>{{ $pengajuan->created_at->format('d F Y, H:i') }} WIB</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('aptika.pengajuan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Daftar Usulan</span>
            </a>
            @if ($pengajuan->status === 'approved' && !$pengajuan->project)
                <a href="{{ route('aptika.project.create', ['pengajuan_id' => $pengajuan->id]) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">assignment_add</span>
                    <span>Inisiasi Proyek</span>
                </a>
            @elseif ($pengajuan->project)
                <a href="{{ route('aptika.project.show', $pengajuan->project) }}" class="px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">developer_board</span>
                    <span>Buka Lembar Proyek</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Detail Deskripsi & Data Pemohon -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-3">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">description</span>
                <span>Deskripsi Kebutuhan & Proses Bisnis Usulan</span>
            </h3>
            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-xl border border-slate-200/60">
                {{ $pengajuan->deskripsi_kebutuhan }}
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-3">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">badge</span>
                <span>Data Pemohon & Narahubung</span>
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Nama Perangkat Daerah (OPD)</span>
                    <span class="font-bold text-slate-900">{{ $pengajuan->instansi->nama_instansi }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Pejabat / PIC Pengaju</span>
                    <span class="font-semibold text-slate-800">{{ $pengajuan->nama_pemohon }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Nomor WhatsApp / Kontak</span>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pengajuan->no_hp_pemohon) }}" target="_blank" class="font-mono text-[#0D47A1] hover:underline flex items-center gap-1">
                        <span>{{ $pengajuan->no_hp_pemohon }}</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lampiran Berkas Dokumen Terisolasi -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">folder_open</span>
                    <span>Berkas Lampiran Regulasi OPD</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Dokumen persyaratan resmi yang diunggah pemohon</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0D47A1] border border-blue-100">
                {{ $pengajuan->dokumen->count() }} Dokumen
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($pengajuan->dokumen as $dok)
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">picture_as_pdf</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ $dok->masterDokumen->nama_dokumen ?? 'Dokumen Pendukung' }}
                                </h4>
                                @if ($dok->masterDokumen?->wajib)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Wajib
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">
                                {{ $dok->nama_file_asli }} • <span class="text-slate-400">{{ $dok->formatted_size }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-mono truncate max-w-lg">
                                Storage: {{ $dok->file_path }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0 sm:self-center">
                        <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-semibold shadow-xs transition">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Buka / Unduh Berkas</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-1">folder_off</span>
                    <p class="text-sm font-semibold">Tidak ada lampiran dokumen pada permohonan ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Lembar Keputusan Telaah Teknis (Reviewer Form) -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">fact_check</span>
                <span>Lembar Keputusan Hasil Telaah Teknis (Admin APTIKA)</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Pilih keputusan hasil audit kelayakan KAK, arsitektur data, dan kepatuhan regulasi SPBE.
            </p>
        </div>

        <form method="POST" action="{{ route('aptika.pengajuan.review', $pengajuan) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Pilihan Keputusan Status -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Under Review -->
                <label class="p-4 rounded-xl border-2 cursor-pointer transition select-none flex flex-col justify-between {{ $pengajuan->status === 'under_review' ? 'border-amber-500 bg-amber-50/30 ring-2 ring-amber-500/10' : 'border-slate-200 hover:border-slate-300' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                            <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                        </span>
                        <input type="radio" name="status" value="under_review" {{ $pengajuan->status === 'under_review' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                    </div>
                    <div class="text-xs font-bold text-slate-900">Dalam Telaah Teknis</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Sedang dalam proses kajian arsitektur oleh tim APTIKA.</div>
                </label>

                <!-- Approved -->
                <label class="p-4 rounded-xl border-2 cursor-pointer transition select-none flex flex-col justify-between {{ $pengajuan->status === 'approved' ? 'border-emerald-600 bg-emerald-50/30 ring-2 ring-emerald-600/10' : 'border-slate-200 hover:border-slate-300' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        </span>
                        <input type="radio" name="status" value="approved" {{ $pengajuan->status === 'approved' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                    </div>
                    <div class="text-xs font-bold text-emerald-800">Setujui Permohonan</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Memenuhi syarat dan siap diinisiasi ke proyek pengembangan.</div>
                </label>

                <!-- Rejected -->
                <label class="p-4 rounded-xl border-2 cursor-pointer transition select-none flex flex-col justify-between {{ $pengajuan->status === 'rejected' ? 'border-rose-500 bg-rose-50/30 ring-2 ring-rose-500/10' : 'border-slate-200 hover:border-slate-300' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">
                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                        </span>
                        <input type="radio" name="status" value="rejected" {{ $pengajuan->status === 'rejected' ? 'checked' : '' }} class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                    </div>
                    <div class="text-xs font-bold text-rose-800">Tolak / Revisi</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Belum memenuhi syarat atau memerlukan revisi KAK.</div>
                </label>
            </div>

            <!-- Catatan Penilaian / Rekomendasi Teknis -->
            <div>
                <label for="catatan_penilaian" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Telaah & Rekomendasi Teknis Bidang APTIKA
                </label>
                <textarea 
                    name="catatan_penilaian" 
                    id="catatan_penilaian" 
                    rows="4" 
                    placeholder="Tuliskan catatan teknis hasil telaah, rekomendasi arsitektur database, atau alasan perbaikan KAK bagi OPD..." 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('catatan_penilaian') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('catatan_penilaian', $pengajuan->catatan_penilaian) }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Catatan ini dapat dibaca oleh operator OPD pemohon pada lembar pelacakan status.</p>
                @error('catatan_penilaian')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Evaluator Information if already evaluated -->
            @if ($pengajuan->penilai)
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
                    <div>
                        Terakhir dinilai oleh: <strong>{{ $pengajuan->penilai->name }}</strong>
                    </div>
                    <div class="text-slate-400">
                        {{ $pengajuan->tanggal_penilaian?->format('d M Y, H:i') }} WIB
                    </div>
                </div>
            @endif

            <!-- Submit Button Bar -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-bold shadow-md shadow-[#0D47A1]/20 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Keputusan Telaah</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
