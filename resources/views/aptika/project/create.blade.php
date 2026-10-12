@extends('layouts.aptika')

@section('title', 'Inisiasi Proyek Baru')
@section('page-title', 'Formulir Inisiasi Proyek Baru')
@section('breadcrumb', 'Manajemen Proyek / Inisiasi Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Inisiasi Proyek & Pembentukan Tim</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Bentuk tim pengembang internal Bidang APTIKA untuk mengerjakan usulan aplikasi yang telah disetujui.
                </p>
            </div>
            <a href="{{ route('aptika.project.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('aptika.project.store') }}" class="space-y-6">
            @csrf

            <!-- 1. Pilih Usulan Pengajuan yang Disetujui -->
            <div>
                <label for="pengajuan_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Usulan OPD yang Disetujui <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="pengajuan_id" 
                    id="pengajuan_id" 
                    onchange="onPengajuanChange(this)"
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('pengajuan_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                    <option value="">-- Pilih Usulan Terverifikasi --</option>
                    @foreach ($pengajuans as $p)
                        <option 
                            value="{{ $p->id }}" 
                            data-nama="{{ $p->nama_aplikasi ?? ($p->aplikasi->nama_aplikasi ?? '') }}"
                            {{ old('pengajuan_id', request('pengajuan_id')) == $p->id ? 'selected' : '' }}
                        >
                            [{{ $p->kode_pengajuan }}] {{ $p->instansi->nama_instansi }} - {{ $p->nama_aplikasi ?? ($p->aplikasi->nama_aplikasi ?? 'Usulan') }}
                        </option>
                    @endforeach
                </select>
                @if ($pengajuans->isEmpty())
                    <p class="text-xs text-amber-600 mt-1.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">info</span>
                        <span>Belum ada usulan pengajuan berstatus 'Disetujui' yang belum memiliki proyek. Silakan telaah usulan masuk terlebih dahulu.</span>
                    </p>
                @endif
                @error('pengajuan_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Nama Proyek Pengembangan -->
            <div>
                <label for="nama_project" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Proyek Pengembangan <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_project" 
                    id="nama_project" 
                    value="{{ old('nama_project', $selectedPengajuan ? ($selectedPengajuan->nama_aplikasi ?? $selectedPengajuan->aplikasi?->nama_aplikasi) : '') }}" 
                    placeholder="Contoh: Pembangunan Sistem Telemedicine Siaga Riau Sehat v1.0" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_project') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                @error('nama_project')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Jadwal & Timeline -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Mulai Sprint <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="tanggal_mulai" 
                        id="tanggal_mulai" 
                        value="{{ old('tanggal_mulai', date('Y-m-d')) }}" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('tanggal_mulai') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    @error('tanggal_mulai')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Target Tanggal Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="tanggal_selesai" 
                        id="tanggal_selesai" 
                        value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+3 months'))) }}" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('tanggal_selesai') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    @error('tanggal_selesai')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 4. Penunjukan Ketua Tim (Fleksibel dari Pool Seluruh Personil) -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <label for="ketua_tim_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Tunjuk Ketua Tim Pengembang (Lead Developer / PM) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-[#0D47A1] font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">tune</span>
                        <span>Peran Per-Proyek Fleksibel</span>
                    </span>
                </div>
                <select 
                    name="ketua_tim_id" 
                    id="ketua_tim_id" 
                    onchange="syncKetuaAndAnggota()"
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('ketua_tim_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                    <option value="">-- Pilih Ketua Tim Pengembang --</option>
                    @foreach ($developers as $dev)
                        <option value="{{ $dev->id }}" {{ old('ketua_tim_id') == $dev->id ? 'selected' : '' }}>
                            {{ $dev->name }} &mdash; {{ $dev->jabatan ?? 'Tenaga Pengembang' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Ketua Tim bertugas mengoordinasikan sprint, membagi tugas teknis, dan mencatat progres pengembangan aplikasi.</p>
                @error('ketua_tim_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Pemilihan Anggota Tim -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Pilih Anggota Tim Pengembang
                    </label>
                    <span class="text-[11px] text-slate-400">Pilih satu atau lebih programmer pendukung</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto p-3 rounded-xl border border-slate-200 bg-slate-50/40">
                    @forelse ($developers as $dev)
                        <label id="member-label-{{ $dev->id }}" class="flex items-center gap-3 p-2.5 rounded-lg bg-white border border-slate-200/80 hover:border-slate-300 cursor-pointer transition select-none">
                            <input 
                                type="checkbox" 
                                name="anggota_tim_ids[]" 
                                value="{{ $dev->id }}" 
                                id="member-checkbox-{{ $dev->id }}"
                                {{ in_array($dev->id, old('anggota_tim_ids', [])) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1] border-slate-300"
                            >
                            <div class="text-xs overflow-hidden flex-1">
                                <div class="font-bold text-slate-900 truncate">{{ $dev->name }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ $dev->jabatan ?? 'Tenaga Pengembang' }}</div>
                            </div>
                            <span id="badge-lead-{{ $dev->id }}" class="hidden text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 shrink-0">
                                Ketua Terpilih
                            </span>
                        </label>
                    @empty
                        <div class="col-span-full text-center py-4 text-xs text-slate-400">
                            Belum ada personil tim pengembang yang terdaftar di master data.
                        </div>
                    @endforelse
                </div>
                @error('anggota_tim_ids')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Group -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('aptika.project.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-bold border border-transparent shadow-md shadow-[#0D47A1]/20 transition flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">assignment_add</span>
                    <span>Inisiasi & Simpan Tim Proyek</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function onPengajuanChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const nama = selectedOption.getAttribute('data-nama');
        const inputProject = document.getElementById('nama_project');
        if (nama && !inputProject.value) {
            inputProject.value = 'Pengembangan Sistem ' + nama;
        }
    }

    function syncKetuaAndAnggota() {
        const ketuaId = document.getElementById('ketua_tim_id').value;
        const checkboxes = document.querySelectorAll('input[name="anggota_tim_ids[]"]');

        checkboxes.forEach(cb => {
            const label = document.getElementById('member-label-' + cb.value);
            const badge = document.getElementById('badge-lead-' + cb.value);
            if (cb.value === ketuaId) {
                cb.checked = false;
                cb.disabled = true;
                if (label) {
                    label.classList.add('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
                    label.classList.remove('cursor-pointer');
                }
                if (badge) badge.classList.remove('hidden');
            } else {
                cb.disabled = false;
                if (label) {
                    label.classList.remove('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
                    label.classList.add('cursor-pointer');
                }
                if (badge) badge.classList.add('hidden');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncKetuaAndAnggota();
    });
</script>
@endsection
