<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin APTIKA') - SIMBANGDA Riau</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <!-- Tailwind CSS (Vite / CDN Fallback) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#eff6ff',
                                100: '#dbeafe',
                                600: '#0D47A1',
                                700: '#1565C0',
                                800: '#0a3578',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        html, body {
            overflow-x: hidden;
            overscroll-behavior-x: none;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1;
        }
    </style>
</head>
<body class="bg-[#f4f6fa] text-slate-800 font-sans antialiased h-screen overflow-hidden flex w-full">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden lg:hidden"></div>

    <!-- Sidebar APTIKA -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 h-full bg-white border-r border-slate-200/80 z-50 transition-transform duration-200 -translate-x-full lg:translate-x-0 lg:static lg:h-full flex flex-col justify-between shrink-0">
        <div class="flex-1 overflow-y-auto">
            <!-- Brand Logo -->
            <div class="min-h-20 py-4 px-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <a href="{{ route('aptika.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 shadow-xs p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Kominfo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-base font-bold text-slate-900 tracking-tight block">SIMBANGDA</span>
                        <span class="text-[10px] font-semibold tracking-wider text-[#0D47A1] uppercase block">Bidang APTIKA</span>
                    </div>
                </a>
                <button id="closeSidebarBtn" class="lg:hidden text-slate-400 hover:text-slate-700">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <!-- Role Badge -->
            <div class="p-3.5 mx-3 my-3 rounded-xl bg-blue-50/70 border border-blue-100">
                <div class="text-[10px] font-bold text-blue-800 uppercase tracking-wider">Aplikasi Informatika (APTIKA)</div>
                <div class="text-xs font-semibold text-slate-700 mt-0.5">
                    Reviewer & Pengelola Siklus Proyek
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1">
                <a href="{{ route('aptika.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('aptika.dashboard') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>Dashboard</span>
                </a>

                @canany(['pengajuan.view', 'project.view'])
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        Manajemen Layanan
                    </div>
                @endcanany

                @can('pengajuan.view')
                    <a href="{{ route('aptika.pengajuan.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('aptika.pengajuan.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">rate_review</span>
                            <span>Telaah Pengajuan</span>
                        </div>
                        @php
                            $pendingCount = \App\Models\Pengajuan::whereIn('status', ['submitted', 'under_review'])->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ request()->routeIs('aptika.pengajuan.*') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endcan

                @can('project.view')
                    <a href="{{ route('aptika.project.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('aptika.project.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">assignment</span>
                        <span>Manajemen Proyek</span>
                    </a>
                @endcan

                @canany(['testing.fungsional.view', 'rapat.view'])
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        Kendali Mutu & Koordinasi
                    </div>
                @endcanany

                @can('testing.fungsional.view')
                    <a href="{{ route('aptika.testing.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('aptika.testing.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">fact_check</span>
                        <span>Uji Fungsional</span>
                    </a>
                @endcan

                @can('rapat.view')
                    <a href="{{ route('aptika.rapat.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('aptika.rapat.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">groups</span>
                        <span>Rapat & Notulensi</span>
                    </a>
                @endcan

                @canany(['layanan.view', 'workflow.view'])
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        Konfigurasi Alur Layanan
                    </div>
                @endcanany

                @can('layanan.view')
                    <a href="{{ route('aptika.layanan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('aptika.layanan.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">category</span>
                        <span>Daftar Layanan</span>
                    </a>
                @endcan

                @can('workflow.view')
                    <a href="{{ route('aptika.workflow.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('aptika.workflow.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">account_tree</span>
                        <span>Master Workflow</span>
                    </a>
                @endcan
            </nav>
        </div>

        <!-- Sidebar Footer (User Info & Logout) -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-[#0D47A1] text-white flex items-center justify-center font-bold text-sm shrink-0 uppercase shadow-xs">
                    {{ substr(auth()->user()->name ?? 'A', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin APTIKA' }}</p>
                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-[#0D47A1] uppercase tracking-wider">
                        {{ auth()->user()->roles->first()?->name ? ucwords(str_replace('_', ' ', auth()->user()->roles->first()->name)) : 'Admin APTIKA' }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 min-w-0 flex flex-col h-full overflow-hidden">
        <!-- Topbar -->
        <header class="min-h-20 py-4 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between shrink-0 z-30">
            <div class="flex items-center gap-3">
                <button id="openSidebarBtn" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">@yield('page-title', 'SIMBANGDA APTIKA')</h1>
                    <div class="text-xs text-slate-500 hidden sm:block mt-0.5">
                        @yield('breadcrumb', 'Bidang Aplikasi Informatika — Diskominfo Provinsi Riau')
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('aptika.pengajuan.index', ['status' => 'submitted']) }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-semibold shadow-sm transition">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    <span>Telaah Usulan Masuk</span>
                </a>
            </div>
        </header>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden flex flex-col justify-between">
            <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
                <!-- Flash Message Alerts -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px] shrink-0 mt-0.5">check_circle</span>
                        <div class="text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
                        <span class="material-symbols-outlined text-rose-600 text-[20px] shrink-0 mt-0.5">error</span>
                        <div class="text-sm font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs">
                        <div class="flex items-center gap-2 font-bold text-sm mb-1 text-rose-900">
                            <span class="material-symbols-outlined text-rose-600 text-[18px]">warning</span>
                            <span>Periksa kembali data formulir:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 pl-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="py-4 px-8 border-t border-slate-200/80 bg-white text-center sm:text-left text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
                <span>&copy; {{ date('Y') }} Dinas Komunikasi, Informatika &amp; Statistik Provinsi Riau.</span>
                <span>Bidang Aplikasi Informatika (APTIKA)</span>
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');

        function toggleSidebar(open) {
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        if (openBtn) openBtn.addEventListener('click', () => toggleSidebar(true));
        if (closeBtn) closeBtn.addEventListener('click', () => toggleSidebar(false));
        if (backdrop) backdrop.addEventListener('click', () => toggleSidebar(false));
    </script>
</body>
</html>
