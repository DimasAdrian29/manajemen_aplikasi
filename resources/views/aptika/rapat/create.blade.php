@extends('layouts.aptika')

@section('title', 'Jadwalkan Rapat Proyek')
@section('page-title', 'Formulir Penjadwalan Rapat Proyek')
@section('breadcrumb', 'Agenda Rapat / Jadwalkan Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Jadwalkan Rapat Koordinasi Proyek</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Hubungkan tim teknis internal Kominfo dan instansi OPD pemohon dalam satu forum rapat. Notifikasi otomatis akan dikirimkan ke akun seluruh peserta.
                </p>
            </div>
            <a href="{{ route('aptika.rapat.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('aptika.rapat.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. Pilih Proyek Terkait -->
            <div>
                <label for="project_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Proyek Aplikasi <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="project_id" 
                    id="project_id" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('project_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                    <option value="">-- Pilih Proyek Terkait --</option>
                    @foreach ($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id', request('project_id')) == $proj->id ? 'selected' : '' }}>
                            {{ $proj->nama_project }} ({{ $proj->aplikasi->instansi->nama_instansi ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Nama Rapat / Agenda -->
            <div>
                <label for="nama_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama / Judul Rapat <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_rapat" 
                    id="nama_rapat" 
                    value="{{ old('nama_rapat') }}" 
                    placeholder="Contoh: Rapat Koordinasi Kickoff & Penyelarasan KAK Modul E-Puskesmas" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('nama_rapat') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    required
                >
                @error('nama_rapat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Waktu & Tempat Rapat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="waktu_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Waktu & Jam Pelaksanaan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        name="waktu_rapat" 
                        id="waktu_rapat" 
                        value="{{ old('waktu_rapat', date('Y-m-d\TH:i', strtotime('+1 day 09:00'))) }}" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('waktu_rapat') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        required
                    >
                    @error('waktu_rapat')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tempat_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tempat Pelaksanaan / Tautan Video Call
                    </label>
                    <input 
                        type="text" 
                        name="tempat_rapat" 
                        id="tempat_rapat" 
                        value="{{ old('tempat_rapat', 'Ruang Rapat Bidang APTIKA Lt. 2 Diskominfo Riau') }}" 
                        placeholder="Ruang Rapat APTIKA Lt. 2 / Link Zoom Meeting" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('tempat_rapat') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    @error('tempat_rapat')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 4. Deskripsi & Pokok Bahasan -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi & Agenda Pembahasan
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3" 
                    placeholder="Tuliskan poin-poin agenda rapat yang akan dibahas bersama tim pengembang dan pihak instansi pemohon..." 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('deskripsi') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Upload Berkas Surat Undangan Rapat (PDF) -->
            <div>
                <label for="file_surat_rapat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Surat Undangan Resmi Rapat (PDF / DOCX)
                </label>
                <input 
                    type="file" 
                    name="file_surat_rapat" 
                    id="file_surat_rapat" 
                    accept=".pdf,.doc,.docx" 
                    class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0D47A1] hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50/50 p-2"
                >
                <p class="text-[11px] text-slate-400 mt-1">Berkas surat undangan bertandatangan Kepala Dinas atau Kepala Bidang.</p>
                @error('file_surat_rapat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 6. Hubungkan Peserta Rapat (Fitur Search & Seleksi Cerdas) -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Hubungkan Peserta Rapat <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400">Gunakan kolom pencarian di bawah untuk menemukan dan memilih personil tim atau OPD.</p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                        <span class="material-symbols-outlined text-[14px]">notifications_active</span>
                        <span>Auto-Notifikasi Terkirim</span>
                    </span>
                </div>

                <!-- Input Pencarian Peserta -->
                <div class="relative">
                    <span class="material-symbols-outlined text-[20px] text-slate-400 absolute left-3.5 top-2.5">search</span>
                    <input 
                        type="text" 
                        id="searchPesertaInput" 
                        oninput="handlePesertaSearch(this.value)" 
                        placeholder="Ketik nama personil, peran, atau OPD (contoh: Ahmad, Programmer, Dinkes)..." 
                        class="w-full pl-11 pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-2xs"
                    >
                    <button 
                        type="button" 
                        id="clearSearchBtn" 
                        onclick="clearPesertaSearch()" 
                        class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 cursor-pointer"
                        title="Hapus pencarian"
                    >
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <!-- Kotak Hasil Pencarian (Hanya Muncul Saat Pengguna Mencari) -->
                <div id="searchResultsBox" class="hidden rounded-xl border border-blue-100 bg-blue-50/30 p-3 space-y-2">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-600">
                        <span>Hasil Pencarian:</span>
                        <span id="searchResultCount" class="text-blue-700 font-mono">0 ditemukan</span>
                    </div>

                    <div id="searchResultsList" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto">
                        <!-- Populated by JS -->
                    </div>

                    <div id="noResultsNotice" class="hidden py-4 text-center text-xs text-slate-400">
                        Tidak ada personil yang cocok dengan kata kunci pencarian.
                    </div>
                </div>

                <!-- Kontainer Peserta Terpilih (Chips / List Terkumpul) -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#0D47A1]">how_to_reg</span>
                            <span>Daftar Peserta Terpilih (<span id="selectedCountBadge" class="text-[#0D47A1]">0</span> Orang)</span>
                        </span>
                        <button type="button" id="clearAllSelectedBtn" onclick="clearAllSelected()" class="hidden text-[11px] font-semibold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer">
                            Hapus Semua
                        </button>
                    </div>

                    <!-- Hidden inputs container for form submission -->
                    <div id="hiddenPesertaInputs"></div>

                    <!-- Selected participants grid -->
                    <div id="selectedPesertaGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto">
                        <!-- Populated by JS -->
                    </div>

                    <!-- Empty State saat belum ada yang dipilih -->
                    <div id="emptySelectedNotice" class="py-6 text-center text-slate-400">
                        <span class="material-symbols-outlined text-[32px] text-slate-300 block mb-1">person_search</span>
                        <p class="text-xs font-medium">Belum ada peserta yang dipilih.</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Ketik di kolom pencarian di atas untuk menemukan dan menambahkan personil yang akan diundang.</p>
                    </div>
                </div>

                @error('peserta_ids')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('aptika.rapat.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-bold shadow-md shadow-[#0D47A1]/20 transition flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Jadwalkan & Kirim Undangan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $preparedUsers = $allUsers->map(function ($u) {
        $roleName = $u->roles->first()?->name ?? 'user';
        $roleLabel = ucwords(str_replace('_', ' ', $roleName));
        $instansiName = $u->instansi?->nama_instansi ?? 'Dinas Kominfotik Riau';

        return [
            'id' => (int) $u->id,
            'name' => (string) $u->name,
            'email' => (string) $u->email,
            'role' => (string) $roleName,
            'role_label' => (string) $roleLabel,
            'instansi' => (string) $instansiName,
            'initials' => strtoupper(substr((string) $u->name, 0, 2)),
        ];
    })->values();

    $initialSelected = old('peserta_ids', $potentialParticipants->pluck('id')->toArray());
    $initialSelected = array_values(array_unique(array_map('intval', (array) $initialSelected)));
@endphp

<script>
    const allUsersData = @json($preparedUsers);
    const userMap = new Map(allUsersData.map(u => [u.id, u]));
    let selectedPesertaIds = new Set(@json($initialSelected));

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function handlePesertaSearch(query) {
        const q = (query || '').trim().toLowerCase();
        const resultsBox = document.getElementById('searchResultsBox');
        const resultsList = document.getElementById('searchResultsList');
        const clearBtn = document.getElementById('clearSearchBtn');
        const countSpan = document.getElementById('searchResultCount');
        const noResults = document.getElementById('noResultsNotice');

        if (!q) {
            resultsBox.classList.add('hidden');
            clearBtn.classList.add('hidden');
            resultsList.innerHTML = '';
            return;
        }

        clearBtn.classList.remove('hidden');
        resultsBox.classList.remove('hidden');

        // Filter pencarian berdasarkan nama, email, role, atau OPD
        const filtered = allUsersData.filter(u => {
            return u.name.toLowerCase().includes(q) ||
                u.email.toLowerCase().includes(q) ||
                u.role_label.toLowerCase().includes(q) ||
                u.instansi.toLowerCase().includes(q);
        });

        countSpan.textContent = `${filtered.length} ditemukan`;

        if (filtered.length === 0) {
            resultsList.innerHTML = '';
            noResults.classList.remove('hidden');
            return;
        }

        noResults.classList.add('hidden');
        resultsList.innerHTML = filtered.map(u => {
            const isSelected = selectedPesertaIds.has(u.id);
            const btnClass = isSelected
                ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100'
                : 'bg-[#0D47A1] text-white hover:bg-blue-800';
            const btnText = isSelected ? '✓ Terpilih' : '+ Tambah';

            return `
                <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-blue-300 transition gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-[#0D47A1] font-bold text-xs flex items-center justify-center shrink-0">
                            ${escapeHtml(u.initials)}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">${escapeHtml(u.name)}</p>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-500 truncate">
                                <span class="inline-block px-1.5 py-0.5 rounded font-medium bg-slate-100 text-slate-600">${escapeHtml(u.role_label)}</span>
                                <span class="text-slate-300">•</span>
                                <span class="truncate">${escapeHtml(u.instansi)}</span>
                            </div>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        onclick="togglePeserta(${u.id})" 
                        class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-lg transition cursor-pointer ${btnClass}"
                        title="${isSelected ? 'Klik untuk membatalkan' : 'Klik untuk menambahkan peserta'}"
                    >
                        ${btnText}
                    </button>
                </div>
            `;
        }).join('');
    }

    function togglePeserta(userId) {
        userId = Number(userId);
        if (selectedPesertaIds.has(userId)) {
            selectedPesertaIds.delete(userId);
        } else {
            selectedPesertaIds.add(userId);
        }
        renderSelectedPeserta();

        const searchInput = document.getElementById('searchPesertaInput');
        if (searchInput && searchInput.value.trim()) {
            handlePesertaSearch(searchInput.value);
        }
    }

    function removePeserta(userId) {
        userId = Number(userId);
        selectedPesertaIds.delete(userId);
        renderSelectedPeserta();

        const searchInput = document.getElementById('searchPesertaInput');
        if (searchInput && searchInput.value.trim()) {
            handlePesertaSearch(searchInput.value);
        }
    }

    function clearPesertaSearch() {
        const searchInput = document.getElementById('searchPesertaInput');
        if (searchInput) {
            searchInput.value = '';
            handlePesertaSearch('');
            searchInput.focus();
        }
    }

    function clearAllSelected() {
        if (selectedPesertaIds.size === 0) return;
        if (confirm('Apakah Anda yakin ingin menghapus semua peserta terpilih?')) {
            selectedPesertaIds.clear();
            renderSelectedPeserta();

            const searchInput = document.getElementById('searchPesertaInput');
            if (searchInput && searchInput.value.trim()) {
                handlePesertaSearch(searchInput.value);
            }
        }
    }

    function renderSelectedPeserta() {
        const countBadge = document.getElementById('selectedCountBadge');
        const clearAllBtn = document.getElementById('clearAllSelectedBtn');
        const emptyNotice = document.getElementById('emptySelectedNotice');
        const grid = document.getElementById('selectedPesertaGrid');
        const hiddenInputs = document.getElementById('hiddenPesertaInputs');

        const count = selectedPesertaIds.size;
        if (countBadge) countBadge.textContent = count;

        if (count === 0) {
            if (clearAllBtn) clearAllBtn.classList.add('hidden');
            if (emptyNotice) emptyNotice.classList.remove('hidden');
            if (grid) grid.innerHTML = '';
            if (hiddenInputs) hiddenInputs.innerHTML = '';
            return;
        }

        if (clearAllBtn) clearAllBtn.classList.remove('hidden');
        if (emptyNotice) emptyNotice.classList.add('hidden');

        // Render hidden inputs
        let hiddenHtml = '';
        let gridHtml = '';

        selectedPesertaIds.forEach(id => {
            hiddenHtml += `<input type="hidden" name="peserta_ids[]" value="${id}">`;

            const u = userMap.get(id);
            if (u) {
                gridHtml += `
                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 bg-white hover:border-slate-300 transition">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-[#0D47A1] font-bold text-xs flex items-center justify-center shrink-0 border border-blue-100">
                                ${escapeHtml(u.initials)}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-800 truncate">${escapeHtml(u.name)}</p>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 truncate">
                                    <span class="inline-block px-1.5 py-0.5 rounded font-medium bg-slate-100 text-slate-600">${escapeHtml(u.role_label)}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="truncate">${escapeHtml(u.instansi)}</span>
                                </div>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            onclick="removePeserta(${u.id})" 
                            class="text-slate-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition cursor-pointer shrink-0" 
                            title="Hapus dari daftar peserta"
                        >
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                `;
            }
        });

        if (hiddenInputs) hiddenInputs.innerHTML = hiddenHtml;
        if (grid) grid.innerHTML = gridHtml;
    }

    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        renderSelectedPeserta();
    });
</script>
@endsection
