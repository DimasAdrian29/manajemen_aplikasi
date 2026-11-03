@extends(auth()->user()->userLayout())

@section('title', 'Detail Monitoring Alur: ' . $project->nama_project)
@section('page-title', 'Detail Monitoring Alur')
@section('breadcrumb', 'Monitoring Alur / ' . $project->nama_project)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Header Kartu Informasi Proyek -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-2">
                <span class="font-mono text-xs font-bold text-[#0D47A1] bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                    {{ $project->pengajuan->kode_pengajuan ?? 'PROJ-' . $project->id }}
                </span>
                @if($project->pengajuan?->layanan)
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border" style="background-color: {{ $project->pengajuan->layanan->warna }}15; color: {{ $project->pengajuan->layanan->warna }}; border-color: {{ $project->pengajuan->layanan->warna }}30;">
                        {{ $project->pengajuan->layanan->nama }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                    <span class="material-symbols-outlined text-[14px]">domain</span>
                    <span>{{ $project->aplikasi->instansi->nama_instansi ?? ($project->pengajuan->instansi->nama_instansi ?? 'Pemerintah Provinsi Riau') }}</span>
                </span>
            </div>

            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                {{ $project->nama_project }}
            </h2>
            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap">
                <span>Mulai: <strong>{{ $project->tanggal_mulai ? $project->tanggal_mulai->format('d M Y') : 'Menyesuaikan' }}</strong></span>
                <span>•</span>
                <span>Target Selesai: <strong>{{ $project->tanggal_selesai ? $project->tanggal_selesai->format('d M Y') : 'Menyesuaikan' }}</strong></span>
                <span>•</span>
                <span>Status Sistem: <strong class="text-slate-800">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('workflow.monitoring.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Lembar Alur</span>
            </button>
        </div>
    </div>

    <!-- Metrik Capaian & Status Posisi Tahapan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Progres Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                <span>Capaian Total Pengerjaan</span>
                <span class="text-[#0D47A1] text-sm">{{ (int) $project->progres_total }}%</span>
            </div>
            <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#0D47A1] to-[#1976D2] rounded-full transition-all duration-500" style="width: {{ min(100, (int) $project->progres_total) }}%;"></div>
            </div>
            <div class="text-[11px] text-slate-400 mt-2">
                Berdasarkan akumulasi bobot tahapan milestone & verifikasi
            </div>
        </div>

        <!-- Tahapan Aktif -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="text-xs font-bold text-slate-500 mb-1">Tahapan Aktif Saat Ini</div>
            <div class="text-base font-extrabold text-[#0D47A1] flex items-center gap-1.5 mt-1">
                <span class="material-symbols-outlined text-[20px] animate-spin text-[#0D47A1]">sync</span>
                <span>{{ $project->currentWorkflow->workflow->nama ?? ucfirst(str_replace('_', ' ', $project->status)) }}</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Penanggung Jawab: <strong class="text-slate-700">{{ $project->currentWorkflow->workflow->role?->name ?? 'Tim Terkait' }}</strong>
            </div>
        </div>

        <!-- Tim Pelaksana -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="text-xs font-bold text-slate-500 mb-1">Personel Tim Pengembang</div>
            <div class="text-sm font-bold text-slate-900 mt-1 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px] text-indigo-600">groups</span>
                <span>{{ $project->tim->count() }} Anggota Ditugaskan</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Ketua Tim: <strong class="text-slate-700">{{ $project->tim->firstWhere('peran', 'ketua')?->user->name ?? 'Belum ditentukan' }}</strong>
            </div>
        </div>
    </div>

    <!-- Peta Garis Waktu Lengkap (Full Detailed Workflow Timeline) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0D47A1] text-[22px]">route</span>
                <span>Garis Waktu & Riwayat Audit Tahapan Alur Kerja</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Rincian setiap tahapan dari awal usulan berkas hingga rilis deployment live.
            </p>
        </div>

        @php
            $workflows = $project->pengajuan?->layanan?->workflows ?? collect();
            $workflowDataCollection = $project->pengajuan?->workflowData ?? collect();
            $currentUrutan = $project->currentWorkflow?->urutan ?? 0;
            $isLive = $project->status === 'live';
        @endphp

        @if($workflows->isNotEmpty())
            <div class="relative border-l-2 border-slate-200 ml-4 sm:ml-6 space-y-8 pl-6 sm:pl-8 py-2">
                @foreach ($workflows as $lw)
                    @php
                        $wfData = $workflowDataCollection->firstWhere('layanan_workflow_id', $lw->id);
                        $isCurrent = $project->current_workflow_id === $lw->id && ! $isLive;
                        $isCompleted = ($isLive || ($currentUrutan > 0 && $lw->urutan < $currentUrutan) || ($wfData && $wfData->status === 'completed'));
                        if ($isCurrent) {
                            $isCompleted = false;
                        }
                        $statusText = $isCompleted ? 'completed' : ($isCurrent ? 'in_progress' : 'pending');
                    @endphp

                    <div class="relative group">
                        <!-- Bullet Node Icon -->
                        <div class="absolute -left-[35px] sm:-left-[43px] top-0 w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shadow-xs transition {{ $isCompleted ? 'bg-emerald-600 text-white ring-4 ring-emerald-50' : ($isCurrent ? 'bg-[#0D47A1] text-white ring-4 ring-blue-100 animate-pulse' : 'bg-slate-200 text-slate-500 ring-4 ring-white') }}">
                            @if($isCompleted)
                                <span class="material-symbols-outlined text-[16px]">check</span>
                            @elseif($isCurrent)
                                <span class="material-symbols-outlined text-[16px]">sync</span>
                            @else
                                <span>{{ $lw->urutan }}</span>
                            @endif
                        </div>

                        <!-- Card Step Content -->
                        <div class="p-5 rounded-2xl border transition {{ $isCompleted ? 'bg-emerald-50/30 border-emerald-200/80' : ($isCurrent ? 'bg-blue-50/50 border-[#0D47A1]/40 ring-1 ring-blue-100' : 'bg-slate-50/40 border-slate-200/80') }}">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-extrabold {{ $isCompleted ? 'text-emerald-950' : ($isCurrent ? 'text-[#0D47A1]' : 'text-slate-800') }}">
                                        {{ $lw->workflow->nama }}
                                    </h4>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $isCompleted ? 'bg-emerald-100 text-emerald-800' : ($isCurrent ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600') }}">
                                        {{ $isCompleted ? 'Selesai' : ($isCurrent ? 'Sedang Berjalan' : 'Menunggu Antrean') }}
                                    </span>
                                </div>

                                <div class="text-xs text-slate-400 flex items-center gap-1.5 font-medium">
                                    <span class="material-symbols-outlined text-[14px]">badge</span>
                                    <span>Penanggung Jawab: <strong class="text-slate-700">{{ $lw->workflow->role?->name ?? 'Kominfo' }}</strong></span>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed mb-3">
                                {{ $lw->workflow->deskripsi ?? 'Tahapan alur proses standar.' }}
                            </p>

                            <!-- Riwayat Eksekusi & Catatan jika Selesai -->
                            @if($wfData && $wfData->completed_at)
                                <div class="pt-3 border-t border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-emerald-600 text-[16px]">verified</span>
                                        <span>Diselesaikan oleh: <strong>{{ $wfData->completedBy->name ?? 'Sistem' }}</strong></span>
                                    </div>
                                    <div>
                                        Waktu: <strong>{{ $wfData->completed_at->format('d F Y, H:i') }} WIB</strong>
                                    </div>
                                </div>

                                @if($wfData->catatan)
                                    <div class="mt-2.5 p-3 rounded-xl bg-white border border-slate-200/80 text-xs text-slate-700">
                                        <span class="font-bold text-slate-500 text-[11px] block mb-0.5">Catatan Verifikasi / Telaah:</span>
                                        <p class="italic text-slate-600">{{ $wfData->catatan }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-slate-400 text-xs">
                Proyek ini belum terhubung dengan alur pipeline dinamis. Status pengerjaan saat ini: <strong>{{ $project->status }}</strong>.
            </div>
        @endif
    </div>

    <!-- Tabular Informasi Tambahan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Deskripsi Usulan Pengajuan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h4 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0D47A1] text-[18px]">description</span>
                <span>Spesifikasi Kebutuhan Sistem</span>
            </h4>
            <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                {{ $project->pengajuan->deskripsi_kebutuhan ?? 'Tidak ada deskripsi kebutuhan terlampir.' }}
            </p>
            <div class="pt-3 border-t border-slate-100 text-xs text-slate-500 space-y-1">
                <div>Pemohon: <strong>{{ $project->pengajuan->nama_pemohon ?? '-' }}</strong></div>
                <div>Kontak Narahubung: <strong>{{ $project->pengajuan->no_hp_pemohon ?? '-' }}</strong></div>
            </div>
        </div>

        <!-- Berkas Dokumen Pengajuan & Regulasi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h4 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-600 text-[18px]">folder</span>
                    <span>Berkas Dokumen Terlampir</span>
                </span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600">
                    {{ $project->pengajuan?->dokumen->count() ?? 0 }} Berkas
                </span>
            </h4>

            <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto pr-1">
                @forelse ($project->pengajuan?->dokumen ?? [] as $dok)
                    <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 line-clamp-1">
                                {{ $dok->masterDokumen->nama_dokumen ?? 'Dokumen Pendukung' }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">
                                {{ $dok->nama_file_asli }}
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-blue-50 text-[#0D47A1] hover:bg-blue-100 font-semibold text-[11px] transition shrink-0">
                            Unduh
                        </a>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Tidak ada berkas dokumen pengajuan yang diunggah.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
