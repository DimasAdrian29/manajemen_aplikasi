<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Masuk - SIMBANGDA Provinsi Riau</title>
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
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 selection:bg-blue-100 selection:text-primary">

    <main class="w-full max-w-[450px] space-y-4">
        <!-- Login Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-[0_10px_30px_rgb(0,0,0,0.04)] p-7 sm:p-9">
            
            <!-- Header Identitas -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-xs p-2 mb-3">
                    <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Diskominfo Riau" class="w-full h-full object-contain">
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">SIMBANGDA</h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    Pemerintah Provinsi Riau &bull; Diskominfo
                </p>
            </div>

            <!-- Ringkasan Info Sistem -->
            <div class="mb-5 p-3 rounded-2xl bg-blue-50/70 border border-blue-100 text-[12px] text-blue-900 text-center leading-relaxed">
                Portal terpadu pengajuan, telaah arsitektur, dan uji keamanan aplikasi bagi Perangkat Daerah.
            </div>

            <!-- Flash Alert Sukses -->
            @if (session('success'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-2">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600 shrink-0">check_circle</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Flash Alert Error -->
            @if (session('error'))
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2">
                    <span class="material-symbols-outlined text-[18px] text-rose-600 shrink-0">error</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

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

            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Username / Email -->
                <div>
                    <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Username atau Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </div>
                        <input 
                            type="text" 
                            name="login" 
                            id="login" 
                            value="{{ old('login') }}" 
                            class="block w-full pl-10 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('login') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                            placeholder="Masukkan username atau email" 
                            required 
                            autofocus
                        >
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">lock</span>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="block w-full pl-10 pr-11 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all" 
                            placeholder="••••••••" 
                            required
                        >
                        <button 
                            type="button" 
                            id="togglePassword" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer focus:outline-none"
                            tabindex="-1"
                        >
                            <span id="eyeIcon" class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-[#0D47A1] focus:ring-[#0D47A1]/20">
                        <span class="text-xs text-slate-600 font-medium">Ingat saya</span>
                    </label>
                </div>

                <!-- Tombol Submit -->
                <button 
                    type="submit" 
                    class="w-full bg-[#0D47A1] hover:bg-[#165eae] text-white font-bold py-2.5 px-4 rounded-xl shadow-sm shadow-[#0D47A1]/25 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer pt-2.5 pb-2.5"
                >
                    <span>Masuk Aplikasi</span>
                    <span class="material-symbols-outlined text-[18px]">login</span>
                </button>
            </form>

            <!-- Box Khusus Pendaftaran OPD -->
            <div class="mt-5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 text-center">
                <p class="text-xs text-slate-500">
                    Belum punya akun Perangkat Daerah?
                </p>
                <a href="{{ route('register') }}" class="font-bold text-[#0D47A1] hover:text-[#165eae] hover:underline text-xs inline-flex items-center gap-1 mt-1">
                    <span>Daftar Akun OPD di sini</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>

            <!-- Accordion Akun Demo (Minimal & Collapsed) -->
            <div class="mt-4 pt-3 border-t border-slate-100">
                <details class="text-xs text-slate-500 group">
                    <summary class="font-semibold text-slate-600 flex items-center justify-between list-none py-1 hover:text-[#0D47A1] cursor-pointer transition select-none">
                        <span class="flex items-center gap-1.5 text-[11px] text-slate-600">
                            <span class="material-symbols-outlined text-[15px] text-[#0D47A1]">vpn_key</span>
                            <span>Akun Demo (Klik untuk Isi Otomatis)</span>
                        </span>
                        <span class="material-symbols-outlined text-[15px] group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-2 space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        <button type="button" onclick="fillAccount('opd_dinkes', 'password123')" class="w-full text-left p-2 rounded-lg bg-blue-50/60 hover:bg-blue-100/70 border border-blue-200/60 transition flex items-center justify-between cursor-pointer">
                            <div>
                                <div class="font-bold text-blue-950 text-xs">Operator OPD (Dinkes)</div>
                                <div class="text-[10px] text-blue-800 font-mono">opd_dinkes &bull; password123</div>
                            </div>
                            <span class="text-[10px] font-bold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200">Pilih</span>
                        </button>

                        <button type="button" onclick="fillAccount('admin_aptika', 'password123')" class="w-full text-left p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex items-center justify-between cursor-pointer">
                            <div>
                                <div class="font-bold text-slate-800 text-xs">Admin APTIKA</div>
                                <div class="text-[10px] text-slate-500 font-mono">admin_aptika &bull; password123</div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">Pilih</span>
                        </button>

                        <button type="button" onclick="fillAccount('admin_persandian', 'password123')" class="w-full text-left p-2 rounded-lg bg-emerald-50/60 hover:bg-emerald-100/70 border border-emerald-200/60 transition flex items-center justify-between cursor-pointer">
                            <div>
                                <div class="font-bold text-emerald-950 text-xs">Admin Persandian (CSIRT)</div>
                                <div class="text-[10px] text-emerald-800 font-mono">admin_persandian &bull; password123</div>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded border border-emerald-200">Pilih</span>
                        </button>

                        <button type="button" onclick="fillAccount('admin_infrastruktur', 'password123')" class="w-full text-left p-2 rounded-lg bg-cyan-50/60 hover:bg-cyan-100/70 border border-cyan-200/60 transition flex items-center justify-between cursor-pointer">
                            <div>
                                <div class="font-bold text-cyan-950 text-xs">Admin Infrastruktur (TIK)</div>
                                <div class="text-[10px] text-cyan-800 font-mono">admin_infrastruktur &bull; password123</div>
                            </div>
                            <span class="text-[10px] font-bold text-cyan-700 bg-white px-2 py-0.5 rounded border border-cyan-200">Pilih</span>
                        </button>

                        <button type="button" onclick="fillAccount('ahmad_lead', 'password123')" class="w-full text-left p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex items-center justify-between cursor-pointer">
                            <div>
                                <div class="font-bold text-slate-800 text-xs">Ketua Tim Pengembang</div>
                                <div class="text-[10px] text-slate-500 font-mono">ahmad_lead &bull; password123</div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">Pilih</span>
                        </button>

                        <button type="button" onclick="fillAccount('kaido', 'wanokuni123')" class="w-full text-left p-2 rounded-lg bg-purple-50/60 hover:bg-purple-100/70 border border-purple-200/60 transition flex items-center justify-between cursor-pointer">
                            <div>
                                <div class="font-bold text-purple-950 text-xs">Superadmin Utama</div>
                                <div class="text-[10px] text-purple-800 font-mono">kaido &bull; wanokuni123</div>
                            </div>
                            <span class="text-[10px] font-bold text-purple-700 bg-white px-2 py-0.5 rounded border border-purple-200">Pilih</span>
                        </button>
                    </div>
                </details>
            </div>

        </div>

        <!-- Footer Ringkas -->
        <p class="text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Diskominfo Provinsi Riau. Seluruh hak cipta dilindungi.
        </p>
    </main>

    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const loginInput = document.getElementById('login');
        const eyeIcon = document.getElementById('eyeIcon');

        if (toggleBtn && passwordInput && eyeIcon) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
            });
        }

        function fillAccount(username, password) {
            if (loginInput) loginInput.value = username;
            if (passwordInput) passwordInput.value = password;
        }
    </script>
</body>
</html>
