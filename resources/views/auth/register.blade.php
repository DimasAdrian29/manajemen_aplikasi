<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pendaftaran Akun OPD - SIMBANGDA Riau</title>
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS with Forms -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0D47A1",
                        "primary-hover": "#165eae",
                    },
                    fontFamily: {
                        "sans": ["Plus Jakarta Sans", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 selection:bg-blue-100 selection:text-primary py-8 sm:py-12">

    <main class="w-full max-w-[560px] space-y-4">
        <!-- Register Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-[0_10px_30px_rgb(0,0,0,0.04)] p-7 sm:p-9">
            
            <!-- Header Identitas -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-xs p-2 mb-3">
                    <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Diskominfo Riau" class="w-full h-full object-contain">
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Akun OPD</h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    Khusus Perwakilan Organisasi Perangkat Daerah &bull; Pemprov Riau
                </p>
            </div>

            <!-- Ringkasan Info Registrasi -->
            <div class="mb-5 p-3 rounded-2xl bg-blue-50/70 border border-blue-100 text-[12px] text-blue-900 leading-relaxed text-center">
                Pendaftaran ini khusus untuk <strong>PIC / Operator OPD</strong> guna mengajukan usulan aplikasi. Akun teknis internal dikelola oleh Superadmin.
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2">
                    <span class="material-symbols-outlined text-[18px] text-rose-600 shrink-0">error</span>
                    <div class="space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <p class="font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form Registrasi -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Instansi OPD -->
                <div>
                    <label for="instansi_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Instansi Perangkat Daerah (OPD) <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="instansi_id" 
                        id="instansi_id" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('instansi_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer transition-all" 
                        required
                    >
                        <option value="">-- Pilih Instansi OPD --</option>
                        @foreach ($instansis as $ins)
                            <option value="{{ $ins->id }}" {{ old('instansi_id') == $ins->id ? 'selected' : '' }}>
                                {{ $ins->nama_instansi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Lengkap PIC -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap PIC / Operator <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        placeholder="Contoh: Budi Santoso, S.Kom" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                        required
                    >
                </div>

                <!-- Username & Email (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username Akun <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-bold">@</span>
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                value="{{ old('username') }}" 
                                placeholder="opd_dinkes_pic" 
                                class="block w-full pl-8 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('username') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}" 
                            placeholder="pic@riau.go.id" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                            required
                        >
                    </div>
                </div>

                <!-- No HP & Jabatan (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="no_hp" 
                            id="no_hp" 
                            value="{{ old('no_hp') }}" 
                            placeholder="0812xxxxxxxx" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('no_hp') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                            required
                        >
                    </div>

                    <div>
                        <label for="jabatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jabatan di OPD
                        </label>
                        <input 
                            type="text" 
                            name="jabatan" 
                            id="jabatan" 
                            value="{{ old('jabatan') }}" 
                            placeholder="Pranata Komputer" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('jabatan') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all"
                        >
                    </div>
                </div>

                <!-- ID Telegram (Opsional) -->
                <div>
                    <label for="telegram_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-sky-600">send</span>
                            <span>ID Telegram</span>
                            <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="telegram_id" 
                        id="telegram_id" 
                        value="{{ old('telegram_id') }}" 
                        placeholder="Contoh: 123456789 (cek via bot @userinfobot)" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('telegram_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all"
                    >
                </div>

                <!-- Kata Sandi & Konfirmasi (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="Minimal 6 karakter" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                            required
                        >
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ulangi Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            placeholder="Ketik ulang kata sandi" 
                            class="block w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                            required
                        >
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button 
                    type="submit" 
                    class="w-full bg-[#0D47A1] hover:bg-[#165eae] text-white font-bold py-2.5 px-4 rounded-xl shadow-sm shadow-[#0D47A1]/25 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer mt-2"
                >
                    <span>Daftarkan Akun OPD</span>
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                </button>
            </form>

            <!-- Tautan Kembali ke Login -->
            <div class="mt-5 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-bold text-[#0D47A1] hover:underline">Masuk di sini</a>
                </p>
            </div>

        </div>

        <!-- Footer Ringkas -->
        <p class="text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Diskominfo Provinsi Riau. Seluruh hak cipta dilindungi.
        </p>
    </main>

</body>
</html>
