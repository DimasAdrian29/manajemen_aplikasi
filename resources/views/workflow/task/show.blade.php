@extends(auth()->user()->userLayout())

@section('title', 'Proses Tahapan: ' . $workflow->nama)
@section('page-title', 'Eksekusi Tahapan Alur Kerja')
@section('breadcrumb', 'Alur Kerja / Antrean Tugas / ' . $workflow->nama)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Flash Notifications -->
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Project Context Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">{{ $workflow->ikon ?? 'account_tree' }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tahapan Aktif Proyek</span>
                        @if($workflow->role)
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Penanggung Jawab: {{ ucwords(str_replace('_', ' ', $workflow->role->name)) }}
                            </span>
                        @endif
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 mt-0.5">{{ $workflow->nama }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $workflow->deskripsi ?? 'Silakan lengkapi parameter verifikasi untuk melanjutkan tahapan.' }}</p>
                </div>
            </div>

            <a href="{{ route('workflow.task.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700 hover:bg-slate-200 transition self-start sm:self-center">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Nama Proyek & Aplikasi</span>
                <span class="font-bold text-slate-800">{{ $project->nama_project }}</span>
                <span class="text-[11px] text-slate-500 block truncate">{{ $project->aplikasi?->nama_aplikasi }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Instansi Pemohon</span>
                <span class="font-bold text-slate-800">{{ $project->aplikasi?->instansi?->nama_instansi ?? '-' }}</span>
                <span class="text-[11px] text-slate-500 block">Pemohon: {{ $project->pengajuan?->nama_pemohon ?? '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[11px]">Layanan & Progres</span>
                <span class="font-bold text-[#0D47A1]">{{ $project->pengajuan?->layanan?->nama ?? 'Layanan' }}</span>
                <span class="text-[11px] text-slate-600 block mt-0.5">Capaian Saat Ini: <strong>{{ $project->progres_total }}%</strong></span>
            </div>
        </div>
    </div>

    <!-- Main Execution Form -->
    <form method="POST" action="{{ route('workflow.task.advance', $project) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Dynamic Fields Component -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-[#0D47A1]">checklist</span>
                <h3 class="text-sm font-bold text-slate-900">Formulir Kelengkapan & Parameter Verifikasi</h3>
            </div>

            <x-workflow-step-fields :workflow="$project->currentWorkflow" :workflowData="$workflowData" />

            <!-- Catatan Pelaksana -->
            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Catatan Evaluasi / Keterangan Pelaksana (Opsional)
                </label>
                <textarea 
                    name="catatan" 
                    rows="3" 
                    placeholder="Tuliskan catatan hasil pemeriksaan atau rekomendasi tahapan ini..."
                    class="w-full text-xs rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] p-3 transition"
                >{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500 text-center sm:text-left">
                Pastikan seluruh parameter yang bertanda <span class="text-rose-500 font-bold">*</span> telah terisi sebelum menyelesaikan tahapan.
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button 
                    type="button" 
                    onclick="document.getElementById('modalRevision').classList.remove('hidden')" 
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-amber-300 text-amber-700 bg-amber-50/70 hover:bg-amber-100 text-xs font-bold transition flex items-center justify-center gap-1.5"
                >
                    <span class="material-symbols-outlined text-[16px]">assignment_return</span>
                    <span>Minta Revisi Tim</span>
                </button>

                <button 
                    type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm transition"
                >
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Luluskan & Selesaikan Tahapan</span>
                </button>
            </div>
        </div>
    </form>

</div>

<!-- Modal Dialog: Minta Revisi Tim -->
<div id="modalRevision" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2 text-amber-600">
                <span class="material-symbols-outlined">assignment_return</span>
                <h3 class="text-sm font-bold text-slate-900">Kembalikan untuk Perbaikan (Revisi)</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalRevision').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('workflow.task.revision', $project) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan Perbaikan & Temuan Kendala <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    name="catatan_revisi" 
                    rows="4" 
                    required 
                    placeholder="Jelaskan secara rinci bagian apa saja yang perlu diperbaiki oleh tim pengembang..."
                    class="w-full text-xs rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3 transition"
                ></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Proyek akan dikembalikan ke status pengerjaan (In Development) bersama catatan ini.</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('modalRevision').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-700 transition">
                    Kirim Catatan Revisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
