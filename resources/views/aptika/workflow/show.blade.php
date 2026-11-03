@extends('layouts.aptika')

@section('title', 'Field Workflow - ' . $workflow->nama)
@section('page-title', 'Konfigurasi Field Input Workflow')
@section('breadcrumb', 'Konfigurasi Alur Layanan / Master Workflow / ' . $workflow->nama)

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-[32px]">{{ $workflow->ikon ?? 'account_tree' }}</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $workflow->nama }}</h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-2xl">
                        {{ $workflow->deskripsi ?? 'Belum ada deskripsi untuk tahapan alur ini.' }}
                    </p>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-400 flex-wrap">
                        <span class="font-mono">slug: {{ $workflow->slug }}</span>
                        <span>•</span>
                        <span>Dipakai oleh: {{ $workflow->layananWorkflows->count() }} layanan</span>
                        <span>•</span>
                        @if ($workflow->role)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="material-symbols-outlined text-[13px]">badge</span>
                                <span>Peran Pelaksana: {{ ucwords(str_replace('_', ' ', $workflow->role->name)) }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] text-slate-400 bg-slate-100 italic">
                                Peran Pelaksana: Umum / Fleksibel
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-center">
                <a href="{{ route('aptika.workflow.edit', $workflow) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    <span>Edit Workflow</span>
                </a>
                <a href="{{ route('aptika.workflow.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Layanan Usage Pill Tags -->
        @if ($workflow->layananWorkflows->isNotEmpty())
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-slate-500">Terpasang pada alur:</span>
                @foreach ($workflow->layananWorkflows as $lw)
                    <a href="{{ route('aptika.layanan.show', $lw->layanan) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold border transition" style="background-color: {{ $lw->layanan->warna }}10; color: {{ $lw->layanan->warna }}; border-color: {{ $lw->layanan->warna }}30;">
                        <span>{{ $lw->layanan->nama }}</span>
                        <span class="text-[10px] opacity-75">(Step #{{ $lw->urutan }})</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Fields Configuration Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-[#0D47A1]">dynamic_form</span>
                    <span>Field Input & Kelengkapan Tahapan</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Konfigurasikan isian checklist, formulir teks, atau berkas unggahan (terhubung ke Master Dokumen) yang wajib dipenuhi pada tahapan ini.
                </p>
            </div>

            <button type="button" onclick="openAddFieldModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition shrink-0 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Tambah Field Input</span>
            </button>
        </div>

        <!-- Table of Fields -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6 w-12 text-center">#</th>
                        <th class="py-3.5 px-6">Label Field / Isian</th>
                        <th class="py-3.5 px-6">Tipe Input</th>
                        <th class="py-3.5 px-6">Dokumen Regulasi Terkait</th>
                        <th class="py-3.5 px-6 text-center">Sifat</th>
                        <th class="py-3.5 px-6 text-center">Urutan</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($workflow->fields as $field)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 text-center font-bold text-xs text-slate-400">
                                {{ $field->urutan }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-xs text-slate-900 block">
                                    {{ $field->label }}
                                </span>
                                @if ($field->keterangan)
                                    <span class="text-[11px] text-slate-400 block mt-0.5">
                                        {{ $field->keterangan }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if ($field->tipe === 'checklist')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="material-symbols-outlined text-[14px]">check_box</span>
                                        <span>Checklist</span>
                                    </span>
                                @elseif ($field->tipe === 'text')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <span class="material-symbols-outlined text-[14px]">edit_note</span>
                                        <span>Input Teks</span>
                                    </span>
                                @elseif ($field->tipe === 'file_upload')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="material-symbols-outlined text-[14px]">upload_file</span>
                                        <span>Upload File</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                @if ($field->masterDokumen)
                                    <div class="flex items-center gap-1.5 font-medium text-[#0D47A1]">
                                        <span class="material-symbols-outlined text-[16px]">description</span>
                                        <span>{{ $field->masterDokumen->nama_dokumen }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $field->masterDokumen->kode_dokumen }} (max {{ number_format($field->masterDokumen->max_size_kb / 1024, 0) }}MB)</span>
                                @else
                                    <span class="text-slate-400 italic text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($field->wajib)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Wajib
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500">
                                        Opsional
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <form method="POST" action="{{ route('aptika.workflow.field.reorder', $workflow) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="field_id" value="{{ $field->id }}">
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" {{ $loop->first ? 'disabled' : '' }} class="p-1 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-30 cursor-pointer disabled:cursor-not-allowed">
                                            <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('aptika.workflow.field.reorder', $workflow) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="field_id" value="{{ $field->id }}">
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" {{ $loop->last ? 'disabled' : '' }} class="p-1 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-30 cursor-pointer disabled:cursor-not-allowed">
                                            <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button 
                                        type="button" 
                                        onclick="openEditFieldModal({{ json_encode($field) }})"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" 
                                        title="Edit Field"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('aptika.workflow.field.destroy', [$workflow, $field]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus field isian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Hapus Field">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">playlist_add</span>
                                <p class="text-sm font-semibold">Belum ada field input pada tahapan workflow ini.</p>
                                <p class="text-xs text-slate-400 mt-1">Tambahkan field checklist, inputan teks, atau slot upload dokumen dari master dokumen regulasi.</p>
                                <button type="button" onclick="openAddFieldModal()" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] transition cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    <span>Tambah Field Pertama</span>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Field -->
<div id="fieldModal" onclick="if(event.target === this) closeFieldModal()" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 id="modalTitle" class="text-sm font-bold text-slate-900">Tambah Field Input Baru</h3>
            <button type="button" onclick="closeFieldModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="fieldForm" method="POST" action="{{ route('aptika.workflow.field.store', $workflow) }}" class="p-5 space-y-4">
            @csrf
            <div id="methodSpoofingContainer"></div>

            <!-- Label -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Label Field <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="fieldLabelInput"
                    name="label" 
                    required 
                    placeholder="Contoh: Upload Proposal Teknis, Checklist Sesuai KAK..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>

            <!-- Tipe Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tipe Input <span class="text-rose-500">*</span>
                </label>
                <select 
                    id="fieldTypeSelect"
                    name="tipe" 
                    required 
                    onchange="handleTypeChange(this.value)"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                    <option value="checklist">Checklist (Centang persetujuan / verifikasi)</option>
                    <option value="text">Inputan Teks (Catatan / URL / Deskripsi)</option>
                    <option value="file_upload">Upload File (Terhubung ke Master Dokumen Regulasi)</option>
                </select>
            </div>

            <!-- Master Dokumen Container (Muncul jika tipe file_upload) -->
            <div id="masterDokumenContainer" class="hidden">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Pilih Master Dokumen Regulasi SPBE
                    </label>
                    <button type="button" onclick="openQuickDokumenModal('create')" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#0D47A1] hover:text-[#1565C0] hover:underline cursor-pointer">
                        <span class="material-symbols-outlined text-[15px]">add_circle</span>
                        <span>+ Tambah Dokumen Baru</span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <select 
                        id="masterDokumenSelect"
                        name="master_dokumen_id" 
                        onchange="handleDokumenSelectChange(this.value)"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                        <option value="">-- Pilih dari Master Dokumen --</option>
                        @foreach ($masterDokumens as $md)
                            <option value="{{ $md->id }}" data-nama="{{ $md->nama_dokumen }}" data-kode="{{ $md->kode_dokumen }}">{{ $md->nama_dokumen }} ({{ $md->kode_dokumen }})</option>
                        @endforeach
                    </select>

                    <div id="selectedDokumenActions" class="hidden flex items-center gap-1 shrink-0">
                        <button type="button" onclick="openQuickDokumenModal('edit')" class="p-2 rounded-xl text-slate-500 hover:text-[#0D47A1] hover:bg-slate-100 border border-slate-200 transition cursor-pointer" title="Ubah Detail Master Dokumen">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button type="button" onclick="deleteQuickDokumen()" class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition cursor-pointer" title="Hapus Master Dokumen">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    Validasi format (.pdf) dan batas ukuran file akan otomatis mengikuti konfigurasi master dokumen.
                </p>
            </div>

            <!-- Keterangan / Helper -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keterangan / Petunjuk Pengisian
                </label>
                <textarea 
                    id="fieldKeteranganInput"
                    name="keterangan" 
                    rows="2" 
                    placeholder="Instruksi bagi pengguna saat mengisi field ini..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                ></textarea>
            </div>

            <!-- Checkbox Wajib -->
            <div class="pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="fieldWajibCheckbox" name="wajib" value="1" class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1]">
                    <span class="text-xs font-bold text-slate-700">Wajib Diisi (Required)</span>
                </label>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeFieldModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] transition">
                    Simpan Field
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sub-Modal Tambah / Edit Quick Master Dokumen -->
<div id="quickDokumenModal" style="z-index: 70;" onclick="if(event.target === this) closeQuickDokumenModal()" class="fixed inset-0 z-[70] bg-slate-900/60 backdrop-blur-[2px] flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0D47A1] text-[20px]">folder_managed</span>
                <h4 id="quickDokumenModalTitle" class="text-xs font-bold text-slate-900">Tambah Jenis Dokumen Baru</h4>
            </div>
            <button type="button" onclick="closeQuickDokumenModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form id="quickDokumenForm" onsubmit="handleQuickDokumenSubmit(event)" class="p-4 space-y-3.5">
            <input type="hidden" id="quickDokumenId">

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Dokumen <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="quickNamaDokumen" 
                    required 
                    placeholder="Contoh: Surat Rekomendasi CSIRT, Berita Acara Uji..." 
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi / Petunjuk Dokumen
                </label>
                <textarea 
                    id="quickDeskripsiDokumen" 
                    rows="2" 
                    placeholder="Instruksi berkas bagi pemohon..." 
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Format Diizinkan
                    </label>
                    <input 
                        type="text" 
                        id="quickFormatDokumen" 
                        value="pdf" 
                        placeholder="pdf" 
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Ukuran Maksimum
                    </label>
                    <select 
                        id="quickMaxSizeDokumen" 
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                        <option value="2048">2 MB</option>
                        <option value="5120">5 MB</option>
                        <option value="10240" selected>10 MB</option>
                        <option value="20480">20 MB</option>
                        <option value="51200">50 MB</option>
                    </select>
                </div>
            </div>

            <div id="quickDokumenError" class="hidden p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs leading-relaxed"></div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeQuickDokumenModal()" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="quickDokumenSubmitBtn" class="px-4 py-1.5 rounded-lg bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <span id="quickDokumenBtnText">Simpan Dokumen</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleTypeChange(val) {
        const mdContainer = document.getElementById('masterDokumenContainer');
        if (val === 'file_upload') {
            mdContainer.classList.remove('hidden');
        } else {
            mdContainer.classList.add('hidden');
        }
    }

    function handleDokumenSelectChange(val) {
        const actionContainer = document.getElementById('selectedDokumenActions');
        if (val) {
            actionContainer.classList.remove('hidden');
        } else {
            actionContainer.classList.add('hidden');
        }
    }

    function openQuickDokumenModal(mode) {
        const modal = document.getElementById('quickDokumenModal');
        const title = document.getElementById('quickDokumenModalTitle');
        const errBox = document.getElementById('quickDokumenError');
        const btnText = document.getElementById('quickDokumenBtnText');
        errBox.classList.add('hidden');
        errBox.textContent = '';

        if (mode === 'create') {
            title.textContent = 'Tambah Jenis Dokumen Baru';
            btnText.textContent = 'Simpan Dokumen';
            document.getElementById('quickDokumenId').value = '';
            document.getElementById('quickNamaDokumen').value = '';
            document.getElementById('quickDeskripsiDokumen').value = '';
            document.getElementById('quickFormatDokumen').value = 'pdf';
            document.getElementById('quickMaxSizeDokumen').value = '10240';
            modal.classList.remove('hidden');
            setTimeout(() => document.getElementById('quickNamaDokumen')?.focus(), 50);
        } else if (mode === 'edit') {
            const select = document.getElementById('masterDokumenSelect');
            const selectedOption = select.options[select.selectedIndex];
            if (!selectedOption || !selectedOption.value) return;

            const docId = selectedOption.value;
            title.textContent = 'Edit Detail Jenis Dokumen';
            btnText.textContent = 'Perbarui Dokumen';
            document.getElementById('quickDokumenId').value = docId;

            fetch(`/aptika/master-dokumen/${docId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    document.getElementById('quickNamaDokumen').value = res.data.nama_dokumen || '';
                    document.getElementById('quickDeskripsiDokumen').value = res.data.deskripsi || '';
                    document.getElementById('quickFormatDokumen').value = res.data.format_diizinkan || 'pdf';
                    document.getElementById('quickMaxSizeDokumen').value = res.data.max_size_kb || '10240';
                    modal.classList.remove('hidden');
                    setTimeout(() => document.getElementById('quickNamaDokumen')?.focus(), 50);
                }
            })
            .catch(() => {
                alert('Gagal mengambil detail jenis dokumen.');
            });
        }
    }

    function closeQuickDokumenModal() {
        document.getElementById('quickDokumenModal').classList.add('hidden');
        document.getElementById('masterDokumenSelect')?.focus();
    }

    function handleQuickDokumenSubmit(e) {
        e.preventDefault();
        const docId = document.getElementById('quickDokumenId').value;
        const isEdit = !!docId;
        const errBox = document.getElementById('quickDokumenError');
        const submitBtn = document.getElementById('quickDokumenSubmitBtn');
        const btnText = document.getElementById('quickDokumenBtnText');

        errBox.classList.add('hidden');
        submitBtn.disabled = true;
        btnText.textContent = 'Menyimpan...';

        const payload = {
            nama_dokumen: document.getElementById('quickNamaDokumen').value,
            deskripsi: document.getElementById('quickDeskripsiDokumen').value,
            format_diizinkan: document.getElementById('quickFormatDokumen').value,
            max_size_kb: parseInt(document.getElementById('quickMaxSizeDokumen').value) || 10240,
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const url = isEdit ? `/aptika/master-dokumen/${docId}` : '/aptika/master-dokumen';
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                let msg = data.message || 'Terjadi kesalahan validasi.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(msg);
            }
            return data;
        })
        .then(res => {
            const select = document.getElementById('masterDokumenSelect');
            if (isEdit) {
                const opt = select.querySelector(`option[value="${docId}"]`);
                if (opt) {
                    opt.textContent = `${res.data.nama_dokumen} (${res.data.kode_dokumen})`;
                    opt.setAttribute('data-nama', res.data.nama_dokumen);
                    opt.setAttribute('data-kode', res.data.kode_dokumen);
                }
            } else {
                const newOpt = document.createElement('option');
                newOpt.value = res.data.id;
                newOpt.textContent = `${res.data.nama_dokumen} (${res.data.kode_dokumen})`;
                newOpt.setAttribute('data-nama', res.data.nama_dokumen);
                newOpt.setAttribute('data-kode', res.data.kode_dokumen);
                select.appendChild(newOpt);
                select.value = res.data.id;
                handleDokumenSelectChange(res.data.id);
            }
            closeQuickDokumenModal();
        })
        .catch(err => {
            errBox.innerHTML = err.message;
            errBox.classList.remove('hidden');
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.textContent = isEdit ? 'Perbarui Dokumen' : 'Simpan Dokumen';
        });
    }

    function deleteQuickDokumen() {
        const select = document.getElementById('masterDokumenSelect');
        const docId = select.value;
        if (!docId) return;

        const selectedOpt = select.options[select.selectedIndex];
        const docName = selectedOpt ? selectedOpt.textContent : 'dokumen ini';

        if (!confirm(`Yakin ingin menghapus ${docName}? Dokumen yang sudah memiliki riwayat berkas pengajuan atau sedang dipakai alur kerja tidak dapat dihapus.`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`/aptika/master-dokumen/${docId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || 'Gagal menghapus jenis dokumen.');
            }
            return data;
        })
        .then(res => {
            alert(res.message);
            const opt = select.querySelector(`option[value="${docId}"]`);
            if (opt) opt.remove();
            select.value = '';
            handleDokumenSelectChange('');
        })
        .catch(err => {
            alert(err.message);
        });
    }

    function openAddFieldModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Field Input Baru';
        document.getElementById('fieldForm').action = "{{ route('aptika.workflow.field.store', $workflow) }}";
        document.getElementById('methodSpoofingContainer').innerHTML = '';
        document.getElementById('fieldLabelInput').value = '';
        document.getElementById('fieldTypeSelect').value = 'checklist';
        document.getElementById('masterDokumenSelect').value = '';
        document.getElementById('fieldKeteranganInput').value = '';
        document.getElementById('fieldWajibCheckbox').checked = false;
        handleTypeChange('checklist');
        handleDokumenSelectChange('');
        document.getElementById('fieldModal').classList.remove('hidden');
    }

    function openEditFieldModal(field) {
        document.getElementById('modalTitle').textContent = 'Edit Field Input';
        let updateUrl = "{{ route('aptika.workflow.field.update', [$workflow, ':id']) }}";
        document.getElementById('fieldForm').action = updateUrl.replace(':id', field.id);
        document.getElementById('methodSpoofingContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('fieldLabelInput').value = field.label;
        document.getElementById('fieldTypeSelect').value = field.tipe;
        document.getElementById('masterDokumenSelect').value = field.master_dokumen_id || '';
        document.getElementById('fieldKeteranganInput').value = field.keterangan || '';
        document.getElementById('fieldWajibCheckbox').checked = !!field.wajib;
        handleTypeChange(field.tipe);
        handleDokumenSelectChange(field.master_dokumen_id || '');
        document.getElementById('fieldModal').classList.remove('hidden');
    }

    function closeFieldModal() {
        document.getElementById('fieldModal').classList.add('hidden');
    }

    // Tangani penekanan tombol Escape secara berjenjang
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            const quickModal = document.getElementById('quickDokumenModal');
            const fieldModal = document.getElementById('fieldModal');

            // 1. Jika sub-modal dokumen terbuka, tutup sub-modal dokumen saja
            if (quickModal && !quickModal.classList.contains('hidden')) {
                closeQuickDokumenModal();
                e.preventDefault();
                e.stopPropagation();
                return;
            }

            // 2. Jika hanya modal field terbuka, baru tutup modal field
            if (fieldModal && !fieldModal.classList.contains('hidden')) {
                closeFieldModal();
                e.preventDefault();
            }
        }
    });
</script>
@endsection
