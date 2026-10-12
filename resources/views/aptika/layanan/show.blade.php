@extends('layouts.aptika')

@section('title', 'Alur Workflow - ' . $layanan->nama)
@section('page-title', 'Susunan Alur Kerja Layanan')
@section('breadcrumb', 'Konfigurasi Alur Layanan / ' . $layanan->nama)

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: {{ $layanan->warna }}18; color: {{ $layanan->warna }};">
                    <span class="material-symbols-outlined text-[32px]">{{ $layanan->ikon ?? 'category' }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-slate-900">{{ $layanan->nama }}</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $layanan->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $layanan->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            <span>{{ $layanan->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-2xl">
                        {{ $layanan->deskripsi ?? 'Belum ada deskripsi untuk layanan ini.' }}
                    </p>
                    <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                        <span class="font-mono">slug: {{ $layanan->slug }}</span>
                        <span>•</span>
                        <span>Urutan katalog: #{{ $layanan->urutan }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-center">
                <a href="{{ route('aptika.layanan.edit', $layanan) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    <span>Edit Informasi</span>
                </a>
                <a href="{{ route('aptika.layanan.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Workflow Pipeline Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-[#0D47A1]">account_tree</span>
                    <span>Susunan Tahapan Alur Kerja (Workflow Pipeline)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Urutan tahapan yang harus dilalui oleh setiap permohonan layanan ini. Naikkan (↑) atau turunkan (↓) urutan prioritas sesuai kebutuhan SOP.
                </p>
            </div>

            <!-- Button Tambah Tahapan Modal Trigger -->
            @if ($availableWorkflows->isNotEmpty())
                <button type="button" onclick="document.getElementById('attachWorkflowModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition shrink-0 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Hubungkan Workflow</span>
                </button>
            @endif
        </div>

        <!-- Workflow Pipeline List / Stepper -->
        <div class="p-6">
            @if ($layanan->workflows->isEmpty())
                <div class="py-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-slate-300 block mb-2">account_tree</span>
                    <p class="text-sm font-bold text-slate-700">Belum ada tahapan workflow yang dihubungkan.</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                        Layanan ini belum memiliki alur tahapan. Silakan hubungkan dari daftar Master Workflow yang tersedia agar OPD dapat memproses pengajuan.
                    </p>
                    @if ($availableWorkflows->isNotEmpty())
                        <button type="button" onclick="document.getElementById('attachWorkflowModal').classList.remove('hidden')" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] transition cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Pilih Workflow Pertama</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($layanan->workflows as $index => $lw)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200/90 hover:border-blue-200 hover:bg-blue-50/20 transition gap-4">
                            <!-- Left: Number & Workflow Info -->
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $lw->urutan }}
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">{{ $lw->workflow->ikon ?? 'check_circle' }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate">
                                            {{ $lw->workflow->nama }}
                                        </h4>
                                        <a href="{{ route('aptika.workflow.show', $lw->workflow) }}" target="_blank" class="text-[11px] font-semibold text-[#0D47A1] hover:underline flex items-center gap-0.5" title="Lihat Master & Field">
                                            <span>Lihat Field</span>
                                            <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                                        </a>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                        {{ $lw->workflow->deskripsi ?? 'Tahapan alur sistem' }}
                                    </p>
                                    <!-- Badges fields inside this workflow -->
                                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                        @php
                                            $totalFields = $lw->workflow->fields->count();
                                            $checklists = $lw->workflow->fields->where('tipe', 'checklist')->count();
                                            $textFields = $lw->workflow->fields->where('tipe', 'text')->count();
                                            $fileUploads = $lw->workflow->fields->where('tipe', 'file_upload')->count();
                                        @endphp
                                        @if ($totalFields > 0)
                                            @if ($fileUploads > 0)
                                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-semibold border border-blue-100">
                                                    {{ $fileUploads }} Dokumen
                                                </span>
                                            @endif
                                            @if ($checklists > 0)
                                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-semibold border border-emerald-100">
                                                    {{ $checklists }} Checklist
                                                </span>
                                            @endif
                                            @if ($textFields > 0)
                                                <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 text-[10px] font-semibold border border-purple-100">
                                                    {{ $textFields }} Isian Teks
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">Belum ada field input pada tahapan ini</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Reorder Buttons & Detach -->
                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Tombol Naik (↑) -->
                                <form method="POST" action="{{ route('aptika.layanan.workflow.reorder', $layanan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="layanan_workflow_id" value="{{ $lw->id }}">
                                    <input type="hidden" name="direction" value="up">
                                    <button 
                                        type="submit" 
                                        {{ $loop->first ? 'disabled' : '' }} 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition {{ $loop->first ? 'text-slate-300 cursor-not-allowed bg-slate-50' : 'text-slate-600 bg-white border border-slate-200 hover:bg-blue-50 hover:text-[#0D47A1] cursor-pointer' }}"
                                        title="Naikkan Urutan"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">arrow_upward</span>
                                    </button>
                                </form>

                                <!-- Tombol Turun (↓) -->
                                <form method="POST" action="{{ route('aptika.layanan.workflow.reorder', $layanan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="layanan_workflow_id" value="{{ $lw->id }}">
                                    <input type="hidden" name="direction" value="down">
                                    <button 
                                        type="submit" 
                                        {{ $loop->last ? 'disabled' : '' }} 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition {{ $loop->last ? 'text-slate-300 cursor-not-allowed bg-slate-50' : 'text-slate-600 bg-white border border-slate-200 hover:bg-blue-50 hover:text-[#0D47A1] cursor-pointer' }}"
                                        title="Turunkan Urutan"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">arrow_downward</span>
                                    </button>
                                </form>

                                <!-- Detach Workflow from Layanan -->
                                <form method="POST" action="{{ route('aptika.layanan.workflow.detach', [$layanan, $lw]) }}" onsubmit="return confirm('Apakah Anda yakin ingin melepaskan tahapan ini dari alur layanan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-rose-500 bg-white border border-rose-200 hover:bg-rose-50 hover:text-rose-700 transition cursor-pointer"
                                        title="Hapus dari Alur Layanan"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Hubungkan Workflow Baru -->
<div id="attachWorkflowModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Hubungkan Master Workflow</h3>
            <button type="button" onclick="document.getElementById('attachWorkflowModal').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('aptika.layanan.workflow.attach', $layanan) }}" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Master Workflow
                </label>
                <select name="workflow_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                    <option value="">-- Pilih Salah Satu Workflow --</option>
                    @foreach ($availableWorkflows as $aw)
                        <option value="{{ $aw->id }}">{{ $aw->nama }} ({{ $aw->fields->count() }} field)</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('attachWorkflowModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] transition">
                    Tambahkan ke Alur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
