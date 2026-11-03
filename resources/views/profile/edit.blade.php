@extends(auth()->user()->userLayout())

@section('title', 'Profil Saya & Pengaturan Akun')
@section('page-title', 'Profil & Pengaturan Akun')
@section('breadcrumb', 'Pengaturan / Profil Pengguna')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header Profil Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[#0D47A1] text-white flex items-center justify-center font-bold text-2xl uppercase shadow-md shadow-[#0D47A1]/20 shrink-0">
                {{ substr($user->name ?? 'U', 0, 2) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-[#0D47A1] uppercase tracking-wide">
                        {{ $user->roles->first()?->name ? ucwords(str_replace('_', ' ', $user->roles->first()->name)) : 'Pengguna Sistem' }}
                    </span>
                    @if ($user->instansi)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                            <span class="material-symbols-outlined text-[14px]">apartment</span>
                            <span>{{ $user->instansi->nama_instansi }}</span>
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-slate-400">alternate_email</span>
                        <span>{{ $user->username ?? '-' }}</span>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-slate-400">mail</span>
                        <span>{{ $user->email }}</span>
                    </span>
                    @if ($user->telegram_id)
                        <span class="text-slate-300">•</span>
                        <span class="flex items-center gap-1 text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md font-medium">
                            <span class="material-symbols-outlined text-[14px]">send</span>
                            <span>ID Telegram: {{ $user->telegram_id }}</span>
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <div class="text-xs text-slate-400 flex items-center gap-1.5 self-start md:self-center bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/60">
            <span class="material-symbols-outlined text-[18px] text-slate-500">verified_user</span>
            <span>Akun Aktif SIMBANGDA Riau</span>
        </div>
    </div>

    <!-- Dua Kolom: Form Profil & Form Kata Sandi -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kartu 1: Informasi Profil & Kontak (Lebar 7 kolom) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                    <div class="p-2 rounded-xl bg-blue-50 text-[#0D47A1]">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Informasi Data Diri & Akun</h3>
                        <p class="text-xs text-slate-500">Perbarui identitas profil, username login, nomor kontak, serta ID Telegram.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $user->name) }}" 
                            placeholder="Contoh: Budi Santoso, S.Kom" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                            required
                        >
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Username / Ganti User -->
                        <div>
                            <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Username <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-bold">@</span>
                                <input 
                                    type="text" 
                                    name="username" 
                                    id="username" 
                                    value="{{ old('username', $user->username) }}" 
                                    placeholder="username_anda" 
                                    class="block w-full pl-8 pr-3.5 py-2.5 bg-slate-50/50 border @error('username') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                                    required
                                >
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk masuk ke sistem.</p>
                            @error('username')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email', $user->email) }}" 
                                placeholder="nama@riau.go.id" 
                                class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                                required
                            >
                            @error('email')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nomor HP / WhatsApp -->
                        <div>
                            <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor HP / WhatsApp
                            </label>
                            <input 
                                type="text" 
                                name="no_hp" 
                                id="no_hp" 
                                value="{{ old('no_hp', $user->no_hp) }}" 
                                placeholder="0812xxxxxxxx" 
                                class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('no_hp') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                            >
                            @error('no_hp')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jabatan / Posisi Teknis -->
                        <div>
                            <label for="jabatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jabatan / Posisi
                            </label>
                            <input 
                                type="text" 
                                name="jabatan" 
                                id="jabatan" 
                                value="{{ old('jabatan', $user->jabatan) }}" 
                                placeholder="Contoh: Pranata Komputer Ahli Muda" 
                                class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('jabatan') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                            >
                            @error('jabatan')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- ID Telegram (Opsional) -->
                    <div class="pt-1">
                        <label for="telegram_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-sky-600">send</span>
                                <span>ID User Telegram</span>
                                <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                            </span>
                        </label>
                        <input 
                            type="text" 
                            name="telegram_id" 
                            id="telegram_id" 
                            value="{{ old('telegram_id', $user->telegram_id) }}" 
                            placeholder="Contoh: 123456789" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('telegram_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                        >
                        @error('telegram_id')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Bantuan Mendapatkan ID Telegram -->
                        <div class="mt-2.5 p-3 rounded-xl bg-sky-50/60 border border-sky-100 text-[11px] text-sky-800 flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-sky-600 shrink-0 mt-0.5">help</span>
                            <div>
                                <span class="font-bold">Cara mendapatkan ID Telegram:</span>
                                Buka aplikasi Telegram, cari bot <strong>@userinfobot</strong> atau <strong>@raw_data_bot</strong>, lalu klik <em>Start</em>. Bot akan membalas dengan angka <code>Id</code> Anda. Masukkan angka tersebut ke kolom ini untuk menerima notifikasi alur kerja.
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-sm shadow-[#0D47A1]/20 transition flex items-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan Perubahan Data</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kartu 2: Keamanan Akun / Ganti Kata Sandi (Lebar 5 kolom) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-700">
                        <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Keamanan &amp; Kata Sandi</h3>
                        <p class="text-xs text-slate-500">Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan data.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Kata Sandi Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="current_password" 
                            id="current_password" 
                            placeholder="Masukkan kata sandi lama" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('current_password') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                            required
                        >
                        @error('current_password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kata Sandi Baru -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="Minimal 6 karakter" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                            required
                        >
                        @error('password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Kata Sandi Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            placeholder="Ketik ulang kata sandi baru" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                            required
                        >
                    </div>

                    <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/60 text-[11px] text-amber-900 flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-amber-700 shrink-0 mt-0.5">shield</span>
                        <span>Pastikan kata sandi baru memiliki minimal 6 karakter serta memadukan huruf dan angka untuk keamanan optimal.</span>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-sm transition flex items-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
