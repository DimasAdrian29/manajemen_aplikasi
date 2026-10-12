@extends('layouts.eksekutif')

@section('title', 'Persetujuan Kendala & Penundaan Proyek')
@section('page-title', 'Persetujuan Kendala & Penundaan Proyek')
@section('breadcrumb', 'Persetujuan Kendala')

@section('content')
<div class="space-y-6">

    <!-- Description Card -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0D47A1] via-[#1565C0] to-slate-900 p-6 sm:p-7 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-blue-900/40">
        <div class="space-y-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-white/20 text-white border border-white/20">
                    Disposisi Kebijakan
                </span>
                <span class="text-xs text-blue-100">Pengendalian Proyek APTIKA</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Antrean Permohonan Penundaan Proyek</h2>
            <p class="text-xs sm:text-sm text-blue-100/90 max-w-2xl leading-relaxed">
                Meninjau laporan hambatan teknis, kendala integrasi, atau ketiadaan infrastruktur yang diajukan oleh Tim Pengembang untuk diberikan keputusan persetujuan target waktu baru atau penolakan.
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-2 bg-white/10 backdrop-blur-xs px-4 py-3 rounded-xl border border-white/15">
            <span class="material-symbols-outlined text-rose-300 text-[28px]">pending_actions</span>
            <div>
                <div class="text-[10px] uppercase font-bold text-rose-200">Perlu Tindakan</div>
                <div class="text-lg font-black text-white">{{ $counts['diajukan'] }} Permohonan</div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('eksekutif.penundaan.index', ['status' => 'diajukan']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'diajukan' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            <span>Menunggu Disposisi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'diajukan' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $counts['diajukan'] }}</span>
        </a>
        <a href="{{ route('eksekutif.penundaan.index', ['status' => 'disetujui']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'disetujui' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            <span>Telah Disetujui</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'disetujui' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $counts['disetujui'] }}</span>
        </a>
        <a href="{{ route('eksekutif.penundaan.index', ['status' => 'ditolak']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'ditolak' ? 'bg-slate-800 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            <span>Ditolak</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'ditolak' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $counts['ditolak'] }}</span>
        </a>
        <a href="{{ route('eksekutif.penundaan.index', ['status' => 'semua']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'semua' ? 'bg-[#0D47A1] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            <span>Semua Riwayat</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'semua' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $counts['total'] }}</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        @if ($penundaans->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[30px]">task_alt</span>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Tidak Ada Permohonan Pada Kategori Ini</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                    Seluruh jadwal pengerjaan proyek berjalan sesuai rencana target awal.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Proyek & Instansi</th>
                            <th class="py-3.5 px-6">Pengaju Permohonan</th>
                            <th class="py-3.5 px-6">Alasan Hambatan / Kendala</th>
                            <th class="py-3.5 px-6">Dokumen Lampiran</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Keputusan Disposisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($penundaans as $p)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6">
                                    <a href="{{ route('eksekutif.project.show', $p->project) }}" class="font-bold text-slate-900 hover:text-[#0D47A1] transition block">
                                        {{ $p->project->nama_project ?? '-' }}
                                    </a>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        {{ $p->project->aplikasi->instansi->nama_instansi ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-xs">
                                    <div class="font-bold text-slate-800">{{ $p->pengaju->name ?? 'Lead Developer' }}</div>
                                    <div class="text-slate-400 font-mono text-[11px] mt-0.5">{{ $p->created_at->format('d M Y - H:i') }}</div>
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <div class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                                        {{ $p->alasan }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-xs whitespace-nowrap">
                                    @if ($p->file_surat)
                                        <a href="{{ asset('storage/' . $p->file_surat) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-[#0D47A1] hover:bg-blue-100 font-bold transition">
                                            <span class="material-symbols-outlined text-[15px]">picture_as_pdf</span>
                                            <span>Surat Kendala</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Tanpa Lampiran</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if ($p->status === 'diajukan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                            <span>Menunggu Disposisi</span>
                                        </span>
                                    @elseif ($p->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            <span>Disetujui</span>
                                        </span>
                                    @elseif ($p->status === 'ditolak')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                                            <span>Ditolak</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    @if ($p->status === 'diajukan')
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Tombol Trigger Modal Approve -->
                                            <button type="button" onclick="openModalApprove({{ $p->id }}, '{{ addslashes($p->project->nama_project) }}')" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1 shadow-xs cursor-pointer">
                                                <span class="material-symbols-outlined text-[16px]">check</span>
                                                <span>Setujui</span>
                                            </button>

                                            <!-- Form Reject Langsung -->
                                            <form method="POST" action="{{ route('eksekutif.penundaan.reject', $p) }}" onsubmit="return confirm('Apakah Anda yakin ingin menolak permohonan penundaan ini? Status proyek akan dikembalikan ke status aktif berjalan.')" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1 cursor-pointer">
                                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                                    <span>Tolak</span>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Selesai Diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($penundaans->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $penundaans->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- Modal Persetujuan Penundaan -->
<div id="modalApprove" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-[22px]">verified</span>
                <span>Persetujuan Penundaan Proyek</span>
            </h3>
            <button type="button" onclick="closeModalApprove()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="formApprove" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Proyek:</span>
                <p id="modalProjectName" class="text-sm font-bold text-slate-900 mt-0.5"></p>
            </div>

            <div>
                <label for="tanggal_selesai_baru" class="block text-xs font-bold text-slate-700 mb-1">
                    Target Tanggal Selesai Baru (Opsional):
                </label>
                <input type="date" id="tanggal_selesai_baru" name="tanggal_selesai_baru" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 focus:border-emerald-600">
                <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika hanya ingin mengonfirmasi status penundaan tanpa mengubah target rilis otomatis.</p>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-900 text-xs leading-relaxed">
                Menyetujui permohonan ini akan mencatat disposisi resmi pimpinan dan memberikan kelonggaran waktu bagi tim teknis untuk menyelesaikan kendala.
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModalApprove()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                    Konfirmasi Persetujuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalApprove(penundaanId, projectName) {
        document.getElementById('modalProjectName').textContent = projectName;
        document.getElementById('formApprove').action = `/eksekutif/penundaan/${penundaanId}/approve`;
        document.getElementById('modalApprove').classList.remove('hidden');
    }

    function closeModalApprove() {
        document.getElementById('modalApprove').classList.add('hidden');
    }
</script>
@endsection
