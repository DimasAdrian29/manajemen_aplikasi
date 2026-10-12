@extends('layouts.opd')

@section('title', 'Buat Permohonan Baru')
@section('page-title', 'Formulir Pengajuan Layanan Aplikasi')
@section('breadcrumb', 'Layanan Aplikasi / Pengajuan Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Notice Card -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100/80 shadow-xs flex items-start gap-4">
        <span class="material-symbols-outlined text-[#0D47A1] text-[28px] shrink-0 mt-0.5">verified_user</span>
        <div class="text-xs text-slate-700 leading-relaxed">
            <span class="font-bold text-[#0D47A1] block text-sm mb-1">Standarisasi & Regulasi Layanan SPBE Daerah</span>
            Setiap usulan pembangunan maupun pengembangan aplikasi daerah dievaluasi oleh Bidang APTIKA, Bidang Persandian, dan Tim Tata Kelola TIK Diskominfo Provinsi Riau. Pastikan seluruh dokumen persyaratan resmi terunggah dengan format dan stempel/TTE yang sah.
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('opd.pengajuan.store') }}" enctype="multipart/form-data" class="space-y-8" id="formPengajuan">
            @csrf

            <!-- 1. Pilihan Jenis Layanan -->
            <div>
                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">
                    Langkah 1: Pilih Jenis Layanan <span class="text-rose-500">*</span>
                </label>
                
                @php
                    $selectedJenis = old('jenis_pengajuan', request('jenis', 'new_app'));
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- New App -->
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition select-none jenis-radio-card {{ $selectedJenis === 'new_app' ? 'border-[#0D47A1] bg-blue-50/40 ring-2 ring-[#0D47A1]/10' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" name="jenis_pengajuan" value="new_app" class="sr-only" {{ $selectedJenis === 'new_app' ? 'checked' : '' }} onchange="onJenisChange(this.value)">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">add_box</span>
                            </span>
                            <span class="radio-check w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center {{ $selectedJenis === 'new_app' ? 'border-[#0D47A1] bg-[#0D47A1]' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-white {{ $selectedJenis === 'new_app' ? 'block' : 'hidden' }}"></span>
                            </span>
                        </div>
                        <div class="text-sm font-bold text-slate-900">Aplikasi Baru</div>
                        <div class="text-[11px] text-slate-500 mt-1">Usulan sistem informasi yang belum pernah dibangun sebelumnya.</div>
                    </label>

                    <!-- App Development -->
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition select-none jenis-radio-card {{ $selectedJenis === 'app_development' ? 'border-[#0D47A1] bg-blue-50/40 ring-2 ring-[#0D47A1]/10' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" name="jenis_pengajuan" value="app_development" class="sr-only" {{ $selectedJenis === 'app_development' ? 'checked' : '' }} onchange="onJenisChange(this.value)">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-[#0D47A1] flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">upgrade</span>
                            </span>
                            <span class="radio-check w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center {{ $selectedJenis === 'app_development' ? 'border-[#0D47A1] bg-[#0D47A1]' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-white {{ $selectedJenis === 'app_development' ? 'block' : 'hidden' }}"></span>
                            </span>
                        </div>
                        <div class="text-sm font-bold text-slate-900">Pengembangan Modul</div>
                        <div class="text-[11px] text-slate-500 mt-1">Pembaruan fitur/modul pada sistem informasi eksisting.</div>
                    </label>

                    <!-- App Deactivation -->
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition select-none jenis-radio-card {{ $selectedJenis === 'app_deactivation' ? 'border-[#0D47A1] bg-blue-50/40 ring-2 ring-[#0D47A1]/10' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" name="jenis_pengajuan" value="app_deactivation" class="sr-only" {{ $selectedJenis === 'app_deactivation' ? 'checked' : '' }} onchange="onJenisChange(this.value)">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">power_off</span>
                            </span>
                            <span class="radio-check w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center {{ $selectedJenis === 'app_deactivation' ? 'border-[#0D47A1] bg-[#0D47A1]' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-white {{ $selectedJenis === 'app_deactivation' ? 'block' : 'hidden' }}"></span>
                            </span>
                        </div>
                        <div class="text-sm font-bold text-slate-900">Penonaktifan Sistem</div>
                        <div class="text-[11px] text-slate-500 mt-1">Penghentian aplikasi, penutupan domain & arsip database.</div>
                    </label>
                </div>
                @error('jenis_pengajuan')
                    <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Informasi Aplikasi -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Langkah 2: Data Sistem & Kebutuhan
                </div>

                <!-- Input Nama Aplikasi (Hanya jika new_app) -->
                <div id="fieldNamaAplikasi" class="{{ $selectedJenis === 'new_app' ? 'block' : 'hidden' }}">
                    <label for="nama_aplikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Usulan Aplikasi <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_aplikasi" 
                        id="nama_aplikasi" 
                        value="{{ old('nama_aplikasi') }}" 
                        placeholder="Contoh: Sistem Informasi Manajemen RSUD Petala Bumi (SIMRS)" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_aplikasi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    @error('nama_aplikasi')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dropdown Pilih Aplikasi Eksisting (Jika app_development / app_deactivation) -->
                <div id="fieldPilihAplikasi" class="{{ in_array($selectedJenis, ['app_development', 'app_deactivation']) ? 'block' : 'hidden' }}">
                    <label for="aplikasi_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pilih Aplikasi Terdaftar <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="aplikasi_id" 
                        id="aplikasi_id" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('aplikasi_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                        <option value="">-- Pilih Aplikasi Milik OPD Anda --</option>
                        @foreach ($aplikasis as $app)
                            <option value="{{ $app->id }}" {{ old('aplikasi_id') == $app->id ? 'selected' : '' }}>
                                {{ $app->nama_aplikasi }} (Status: {{ ucfirst($app->status) }})
                            </option>
                        @endforeach
                    </select>
                    @if ($aplikasis->isEmpty())
                        <p class="text-xs text-amber-600 mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">info</span>
                            <span>Instansi Anda belum memiliki aplikasi terdaftar. Silakan pilih "Aplikasi Baru".</span>
                        </p>
                    @endif
                    @error('aplikasi_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Kebutuhan & Ruang Lingkup -->
                <div>
                    <label for="deskripsi_kebutuhan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Deskripsi Kebutuhan / Urgensi Sistem <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="deskripsi_kebutuhan" 
                        id="deskripsi_kebutuhan" 
                        rows="4" 
                        placeholder="Jelaskan latar belakang, proses bisnis utama yang ingin diotomasi, pengguna sasaran, serta dampak bagi efisiensi birokrasi..." 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('deskripsi_kebutuhan') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >{{ old('deskripsi_kebutuhan') }}</textarea>
                    @error('deskripsi_kebutuhan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 3. Narahubung Pemohon -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Langkah 3: Narahubung & Pejabat Pemohon
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_pemohon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Pejabat / PIC <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_pemohon" 
                            id="nama_pemohon" 
                            value="{{ old('nama_pemohon', auth()->user()->name) }}" 
                            placeholder="Nama Lengkap Pejabat / Pengelola TIK" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_pemohon') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                            required
                        >
                        @error('nama_pemohon')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_hp_pemohon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="no_hp_pemohon" 
                            id="no_hp_pemohon" 
                            value="{{ old('no_hp_pemohon', auth()->user()->no_hp) }}" 
                            placeholder="0812xxxxxxxx" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('no_hp_pemohon') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                            required
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk konfirmasi jadwal telaah & koordinasi rapat teknis.</p>
                        @error('no_hp_pemohon')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 4. Unggah Berkas Dokumen Dinamis (Sesuai Regulasi Aktif) -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Langkah 4: Lampiran Dokumen Persyaratan
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Slot unggahan di bawah ini disesuaikan otomatis dengan regulasi permohonan yang Anda pilih.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="material-symbols-outlined text-[14px]">security</span>
                        <span>Folder Terisolasi</span>
                    </span>
                </div>

                <div class="space-y-4" id="dokumenContainer">
                    @foreach ($masterDokumens as $doc)
                        @php
                            $isRelevant = ($doc->berlaku_untuk === 'semua' || $doc->berlaku_untuk === $selectedJenis);
                        @endphp
                        <div 
                            class="doc-slot p-5 rounded-xl border border-slate-200/90 bg-slate-50/40 space-y-3 transition" 
                            data-berlaku="{{ $doc->berlaku_untuk }}"
                            data-wajib="{{ $doc->wajib ? '1' : '0' }}"
                            data-doc-id="{{ $doc->id }}"
                            style="{{ $isRelevant ? '' : 'display: none;' }}"
                        >
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#0D47A1] flex items-center justify-center font-bold text-xs">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                            <span>{{ $doc->nama_dokumen }}</span>
                                            @if ($doc->wajib)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Wajib
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                    Opsional
                                                </span>
                                            @endif
                                        </h4>
                                    </div>
                                </div>

                                <!-- Template Download Link (jika ada) -->
                                @if ($doc->file_template)
                                    <a href="{{ asset('storage/' . $doc->file_template) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shrink-0">
                                        <span class="material-symbols-outlined text-[15px]">file_download</span>
                                        <span>Unduh Format Blangko</span>
                                    </a>
                                @endif
                            </div>

                            @if ($doc->deskripsi)
                                <p class="text-xs text-slate-600 bg-white/70 p-2.5 rounded-lg border border-slate-200/60 leading-relaxed">
                                    {{ $doc->deskripsi }}
                                </p>
                            @endif

                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                <input 
                                    type="file" 
                                    name="dokumen[{{ $doc->id }}]" 
                                    id="dokumen_{{ $doc->id }}"
                                    accept="{{ '.' . str_replace([' ', ','], ',.', $doc->format_diizinkan) }}"
                                    class="doc-file-input block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0D47A1] file:text-white hover:file:bg-[#1565C0] cursor-pointer border border-slate-200 rounded-xl bg-white p-2"
                                    {{ ($doc->wajib && $isRelevant) ? 'required' : '' }}
                                    {{ $isRelevant ? '' : 'disabled' }}
                                >
                                <div class="text-[11px] text-slate-400 shrink-0">
                                    Format: <strong class="text-slate-600 uppercase">{{ $doc->format_diizinkan }}</strong> (Maks. {{ number_format($doc->max_size_kb / 1024, 1) }} MB)
                                </div>
                            </div>
                            @error("dokumen.{$doc->id}")
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('opd.pengajuan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-bold shadow-md shadow-[#0D47A1]/20 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Kirim Permohonan ke Kominfo</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function onJenisChange(jenis) {
        // 1. Update radio styling
        document.querySelectorAll('.jenis-radio-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            const checkIcon = card.querySelector('.radio-check');
            const dot = checkIcon.querySelector('span');

            if (input.value === jenis) {
                card.classList.add('border-[#0D47A1]', 'bg-blue-50/40', 'ring-2', 'ring-[#0D47A1]/10');
                card.classList.remove('border-slate-200', 'bg-white');
                checkIcon.classList.add('border-[#0D47A1]', 'bg-[#0D47A1]');
                checkIcon.classList.remove('border-slate-300');
                dot.classList.remove('hidden');
            } else {
                card.classList.remove('border-[#0D47A1]', 'bg-blue-50/40', 'ring-2', 'ring-[#0D47A1]/10');
                card.classList.add('border-slate-200', 'bg-white');
                checkIcon.classList.remove('border-[#0D47A1]', 'bg-[#0D47A1]');
                checkIcon.classList.add('border-slate-300');
                dot.classList.add('hidden');
            }
        });

        // 2. Toggle App Name vs Select Existing App
        const fieldNama = document.getElementById('fieldNamaAplikasi');
        const inputNama = document.getElementById('nama_aplikasi');
        const fieldPilih = document.getElementById('fieldPilihAplikasi');
        const selectApp = document.getElementById('aplikasi_id');

        if (jenis === 'new_app') {
            fieldNama.classList.remove('hidden');
            inputNama.setAttribute('required', 'required');
            fieldPilih.classList.add('hidden');
            selectApp.removeAttribute('required');
        } else {
            fieldNama.classList.add('hidden');
            inputNama.removeAttribute('required');
            fieldPilih.classList.remove('hidden');
            selectApp.setAttribute('required', 'required');
        }

        // 3. Filter Dynamic Document Slots
        document.querySelectorAll('.doc-slot').forEach(slot => {
            const berlaku = slot.getAttribute('data-berlaku');
            const isWajib = slot.getAttribute('data-wajib') === '1';
            const fileInput = slot.querySelector('.doc-file-input');

            const isMatch = (berlaku === 'semua' || berlaku === jenis);

            if (isMatch) {
                slot.style.display = '';
                fileInput.removeAttribute('disabled');
                if (isWajib) {
                    fileInput.setAttribute('required', 'required');
                } else {
                    fileInput.removeAttribute('required');
                }
            } else {
                slot.style.display = 'none';
                fileInput.setAttribute('disabled', 'disabled');
                fileInput.removeAttribute('required');
                fileInput.value = ''; // Reset file selection
            }
        });
    }

    // Inisialisasi awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        const checkedRadio = document.querySelector('input[name="jenis_pengajuan"]:checked');
        if (checkedRadio) {
            onJenisChange(checkedRadio.value);
        }
    });
</script>
@endsection
