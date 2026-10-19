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
<div id="fieldModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
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
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Master Dokumen Regulasi SPBE
                </label>
                <select 
                    id="masterDokumenSelect"
                    name="master_dokumen_id" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                    <option value="">-- Pilih dari Master Dokumen --</option>
                    @foreach ($masterDokumens as $md)
                        <option value="{{ $md->id }}">{{ $md->nama_dokumen }} ({{ $md->kode_dokumen }})</option>
                    @endforeach
                </select>
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

<script>
    function handleTypeChange(val) {
        const mdContainer = document.getElementById('masterDokumenContainer');
        if (val === 'file_upload') {
            mdContainer.classList.remove('hidden');
        } else {
            mdContainer.classList.add('hidden');
        }
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
        document.getElementById('fieldModal').classList.remove('hidden');
    }

    function closeFieldModal() {
        document.getElementById('fieldModal').classList.add('hidden');
    }
</script>
@endsection
