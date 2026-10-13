@php
    use App\Models\InventarisAplikasi;

    $inputClass = 'w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]';
    $labelClass = 'block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2';
    $sectionClass = 'bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden';
@endphp

<div class="space-y-6">
    <!-- Identitas Aplikasi -->
    <div class="{{ $sectionClass }}">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">badge</span>
            <h3 class="text-sm font-bold text-slate-900">Identitas Aplikasi</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-4 gap-5">
            <div>
                <label class="{{ $labelClass }}">No. Registrasi</label>
                <input type="number" name="nomor_registrasi" min="1" value="{{ old('nomor_registrasi', $aplikasi->nomor_registrasi) }}" placeholder="Contoh: 163" class="{{ $inputClass }}">
            </div>
            <div class="sm:col-span-3">
                <label class="{{ $labelClass }}">Nama Aplikasi <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_aplikasi" required value="{{ old('nama_aplikasi', $aplikasi->nama_aplikasi) }}" placeholder="Contoh: Sistem Informasi Kinerja Pegawai (e-Sikap)" class="{{ $inputClass }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Pengelola (OPD) <span class="text-rose-500">*</span></label>
                <select name="instansi_id" required class="{{ $inputClass }}">
                    <option value="">-- Pilih OPD Pengelola --</option>
                    @foreach ($instansiList as $instansi)
                        <option value="{{ $instansi->id }}" @selected(old('instansi_id', $aplikasi->instansi_id) == $instansi->id)>{{ $instansi->nama_instansi }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">OPD belum terdaftar? Tambahkan lewat menu Master Instansi oleh Superadmin.</p>
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">PIC Aplikasi</label>
                <input type="text" name="pic_aplikasi" value="{{ old('pic_aplikasi', $aplikasi->pic_aplikasi) }}" placeholder="Contoh: Tenaga IT, Kepala Bidang ..." class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Status</label>
                <select name="status" id="statusSelect" class="{{ $inputClass }}">
                    <option value="">- Belum ditentukan -</option>
                    @foreach (InventarisAplikasi::STATUS as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $aplikasi->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3" id="penggantiWrapper">
                <label class="{{ $labelClass }}">Aplikasi Pengganti <span class="text-rose-500">*</span></label>
                <select name="aplikasi_pengganti_id" class="{{ $inputClass }}">
                    <option value="">-- Pilih aplikasi yang menggantikan --</option>
                    @foreach ($penggantiList as $pengganti)
                        <option value="{{ $pengganti->id }}" @selected(old('aplikasi_pengganti_id', $aplikasi->aplikasi_pengganti_id) == $pengganti->id)>
                            {{ $pengganti->nomor_registrasi ? $pengganti->nomor_registrasi.' – ' : '' }}{{ $pengganti->nama_aplikasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-4 flex flex-wrap items-center gap-6">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_aplikasi_pemprov" value="1" @checked(old('is_aplikasi_pemprov', $aplikasi->is_aplikasi_pemprov)) class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1]">
                    <span class="text-xs font-semibold text-slate-700">Aplikasi Pemprov Riau</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_website_resmi" value="1" @checked(old('is_website_resmi', $aplikasi->is_website_resmi)) class="w-4 h-4 rounded text-[#0D47A1] focus:ring-[#0D47A1]">
                    <span class="text-xs font-semibold text-slate-700">Website Resmi OPD/UPT</span>
                </label>
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Deskripsi</label>
                <textarea name="deskripsi" rows="4" placeholder="Gambaran umum aplikasi..." class="{{ $inputClass }}">{{ old('deskripsi', $aplikasi->deskripsi) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Fungsi / Menu</label>
                <textarea name="fungsi_menu" rows="4" placeholder="Fungsi utama atau daftar menu aplikasi..." class="{{ $inputClass }}">{{ old('fungsi_menu', $aplikasi->fungsi_menu) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Detail Teknis -->
    <div class="{{ $sectionClass }}">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">dns</span>
            <h3 class="text-sm font-bold text-slate-900">Detail Teknis & Layanan</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label class="{{ $labelClass }}">Platform</label>
                <select name="platform" class="{{ $inputClass }}">
                    <option value="">-</option>
                    @foreach (InventarisAplikasi::PLATFORM as $value => $label)
                        <option value="{{ $value }}" @selected(old('platform', $aplikasi->platform) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Jenis Layanan</label>
                <select name="jenis_layanan" class="{{ $inputClass }}">
                    <option value="">-</option>
                    @foreach (InventarisAplikasi::JENIS_LAYANAN as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis_layanan', $aplikasi->jenis_layanan) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Kategori Konektivitas</label>
                <select name="konektivitas" class="{{ $inputClass }}">
                    <option value="">-</option>
                    @foreach (InventarisAplikasi::KONEKTIVITAS as $value => $label)
                        <option value="{{ $value }}" @selected(old('konektivitas', $aplikasi->konektivitas) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">URL</label>
                <input type="text" name="url" value="{{ old('url', $aplikasi->url) }}" placeholder="contoh.riau.go.id" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Tanggal Pemberlakuan</label>
                <input type="date" name="tanggal_pemberlakuan" value="{{ old('tanggal_pemberlakuan', $aplikasi->tanggal_pemberlakuan?->format('Y-m-d')) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Penempatan Server</label>
                <input type="text" name="penempatan_server" list="serverList" value="{{ old('penempatan_server', $aplikasi->penempatan_server) }}" placeholder="Diskominfotik / PDN / ..." class="{{ $inputClass }}">
                <datalist id="serverList">
                    @foreach (InventarisAplikasi::PENEMPATAN_SERVER as $server)
                        <option value="{{ $server }}">
                    @endforeach
                </datalist>
            </div>
        </div>
    </div>

    <!-- Tim Teknis -->
    <div class="{{ $sectionClass }}">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">engineering</span>
                <h3 class="text-sm font-bold text-slate-900">Tim Teknis</h3>
            </div>
            <span class="text-[11px] text-slate-400">Tulis satu nama per baris</span>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach (InventarisAplikasi::TIM_TEKNIS as $kolom => $label)
                <div>
                    <label class="{{ $labelClass }}">{{ $label }}</label>
                    <textarea name="{{ $kolom }}" rows="4" placeholder="Nama, gelar&#10;Nama, gelar" class="{{ $inputClass }}">{{ old($kolom, $aplikasi->{$kolom}) }}</textarea>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Status Pengembangan & Dokumentasi -->
    <div class="{{ $sectionClass }}">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">task_alt</span>
            <h3 class="text-sm font-bold text-slate-900">Status Pengembangan & Dokumentasi Administrasi</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="{{ $labelClass }}">Status Pengembangan</label>
                <select name="status_pengembangan" class="{{ $inputClass }}">
                    <option value="">- Belum ditentukan -</option>
                    @foreach (InventarisAplikasi::STATUS_PENGEMBANGAN as $value => $label)
                        <option value="{{ $value }}" @selected(old('status_pengembangan', $aplikasi->status_pengembangan) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Kelengkapan Dokumen</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (InventarisAplikasi::DOKUMEN as $kolom => $label)
                        <label class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="{{ $kolom }}" value="1" @checked(old($kolom, $aplikasi->{$kolom})) class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-semibold text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Catatan</label>
                <textarea name="catatan" rows="3" placeholder="Catatan tindak lanjut, kendala, atau informasi tambahan..." class="{{ $inputClass }}">{{ old('catatan', $aplikasi->catatan) }}</textarea>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const statusSelect = document.getElementById('statusSelect');
        const penggantiWrapper = document.getElementById('penggantiWrapper');

        function togglePengganti() {
            penggantiWrapper.classList.toggle('hidden', statusSelect.value !== 'diganti');
        }

        statusSelect.addEventListener('change', togglePengganti);
        togglePengganti();
    })();
</script>
