<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login - SIMBANGDA Riau</title>
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS with Forms and Container Queries -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0D47A1",
                        "primary-container": "#0d47a1",
                        "on-primary": "#ffffff",
                        "secondary": "#165eae",
                        "surface": "#f8fafc",
                        "surface-container": "#e7eeff",
                        "on-surface": "#111c2d",
                        "on-surface-variant": "#64748b",
                        "outline-variant": "#e2e8f0",
                        "error": "#ba1a1a"
                    },
                    fontFamily: {
                        "body-md": ["Plus Jakarta Sans", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        html {
            overscroll-behavior: none;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body class="bg-[#f4f6fa] text-on-surface font-body-md min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 antialiased selection:bg-blue-100 selection:text-primary">
<main class="w-full max-w-[440px]">
    <!-- Clean Minimal Login Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-10">
        <!-- Header Group -->
        <div class="text-center mb-8">
            <!-- Logo -->
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white border border-slate-200/80 shadow-sm p-2 mb-4">
                <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Kominfo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                SIMBANGDA
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed font-normal">
                Sistem Manajemen Pengembangan Aplikasi (SIMBANGDA)<br/>Diskominfo Provinsi Riau
            </p>
        </div>

        <!-- Flash Messages & Error Alerts -->
        @if (session('success'))
            <div class="mb-5 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs sm:text-sm flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[20px] text-emerald-600 shrink-0">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-3.5 rounded-lg bg-red-50 border border-red-200/80 text-red-800 text-xs sm:text-sm flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[20px] text-red-600 shrink-0">error</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-lg bg-red-50 border border-red-200/80 text-red-800 text-xs sm:text-sm flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[20px] text-red-600 shrink-0">error</span>
                <div class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Main Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Username / Email Input -->
            <div>
                <label for="login" class="block text-sm font-semibold text-slate-700 mb-2">
                    Username atau Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </div>
                    <input 
                        type="text" 
                        name="login" 
                        id="login" 
                        value="{{ old('login') }}" 
                        class="block w-full pl-10 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('login') border-red-400 @else border-slate-200 @enderror rounded-lg text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all duration-150" 
                        placeholder="Masukkan username atau email" 
                        required 
                        autofocus
                    >
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Kata Sandi
                    </label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="block w-full pl-10 pr-11 py-2.5 bg-slate-50/50 hover:bg-white text-slate-900 border @error('password') border-red-400 @else border-slate-200 @enderror rounded-lg text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] transition-all duration-150" 
                        placeholder="••••••••" 
                        required
                    >
                    <button 
                        type="button" 
                        id="togglePassword" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer focus:outline-none"
                        tabindex="-1"
                        aria-label="Toggle password visibility"
                    >
                        <span id="eyeIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-[#0D47A1] focus:ring-[#0D47A1]/20">
                    <span class="text-xs sm:text-sm text-slate-600 font-medium">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-[#0D47A1] hover:bg-[#165eae] text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm shadow-[#0D47A1]/20 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Masuk Aplikasi</span>
                <span class="material-symbols-outlined text-[18px]">login</span>
            </button>
        </form>

        <!-- Quick Demo / Info Accordion for Testing -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <details class="text-xs text-slate-500 group" open>
                <summary class="font-medium text-slate-600 flex items-center justify-between list-none py-1 hover:text-[#0D47A1] cursor-pointer transition select-none">
                    <span class="flex items-center gap-1.5 font-bold text-slate-700">
                        <span class="material-symbols-outlined text-[16px] text-[#0D47A1]">key</span>
                        <span>Akun Demo (Klik untuk Isi Otomatis)</span>
                    </span>
                    <span class="material-symbols-outlined text-[16px] group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <div class="mt-2.5 space-y-1.5 max-h-60 overflow-y-auto pr-1">
                    <button type="button" onclick="fillAccount('admin_persandian', 'password123')" class="w-full text-left p-2 rounded-lg bg-emerald-50/70 hover:bg-emerald-100/70 border border-emerald-200/60 transition flex items-center justify-between group/btn cursor-pointer">
                        <div>
                            <div class="font-bold text-emerald-950 text-xs">Admin Persandian (CSIRT)</div>
                            <div class="text-[10px] text-emerald-800 font-mono">admin_persandian &bull; password123</div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded border border-emerald-200">Pilih</span>
                    </button>

                    <button type="button" onclick="fillAccount('admin_infrastruktur', 'password123')" class="w-full text-left p-2 rounded-lg bg-cyan-50/70 hover:bg-cyan-100/70 border border-cyan-200/60 transition flex items-center justify-between group/btn cursor-pointer">
                        <div>
                            <div class="font-bold text-cyan-950 text-xs">Admin Infrastruktur (TIK)</div>
                            <div class="text-[10px] text-cyan-800 font-mono">admin_infrastruktur &bull; password123</div>
                        </div>
                        <span class="text-[10px] font-bold text-cyan-700 bg-white px-2 py-0.5 rounded border border-cyan-200">Pilih</span>
                    </button>

                    <button type="button" onclick="fillAccount('admin_aptika', 'password123')" class="w-full text-left p-2 rounded-lg bg-blue-50/70 hover:bg-blue-100/70 border border-blue-200/60 transition flex items-center justify-between group/btn cursor-pointer">
                        <div>
                            <div class="font-bold text-blue-950 text-xs">Admin APTIKA</div>
                            <div class="text-[10px] text-blue-800 font-mono">admin_aptika &bull; password123</div>
                        </div>
                        <span class="text-[10px] font-bold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200">Pilih</span>
                    </button>

                    <button type="button" onclick="fillAccount('kaido', 'wanokuni123')" class="w-full text-left p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex items-center justify-between group/btn cursor-pointer">
                        <div>
                            <div class="font-bold text-slate-800 text-xs">Superadmin Utama</div>
                            <div class="text-[10px] text-slate-500 font-mono">kaido &bull; wanokuni123</div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">Pilih</span>
                    </button>

                    <button type="button" onclick="fillAccount('opd_dinkes', 'password123')" class="w-full text-left p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex items-center justify-between group/btn cursor-pointer">
                        <div>
                            <div class="font-bold text-slate-800 text-xs">Operator OPD (Dinkes)</div>
                            <div class="text-[10px] text-slate-500 font-mono">opd_dinkes &bull; password123</div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">Pilih</span>
                    </button>

                    <button type="button" onclick="fillAccount('opd_bapenda', 'password123')" class="w-full text-left p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex items-center justify-between group/btn cursor-pointer">
                        <div>
                            <div class="font-bold text-slate-800 text-xs">Operator OPD (Bapenda)</div>
                            <div class="text-[10px] text-slate-500 font-mono">opd_bapenda &bull; password123</div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">Pilih</span>
                    </button>

                    <button type="button" onclick="fillAccount('ahmad_lead', 'password123')" class="w-full text-left p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex items-center justify-between group/btn cursor-pointer">
                        <div>
                            <div class="font-bold text-slate-800 text-xs">Ketua Tim Pengembang</div>
                            <div class="text-[10px] text-slate-500 font-mono">ahmad_lead &bull; password123</div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">Pilih</span>
                    </button>
                </div>
            </details>
        </div>
    </div>

    <!-- Simple Footer -->
    <div class="mt-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Diskominfo Provinsi Riau. Seluruh hak cipta dilindungi.
    </div>
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
