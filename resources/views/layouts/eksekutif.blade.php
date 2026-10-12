<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Pimpinan Eksekutif') - SIMBANGDA Riau</title>

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
        @media print {
            html, body {
                overflow: visible !important;
                height: auto !important;
            }
            aside, header, footer, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .main-scroll-container {
                overflow: visible !important;
                height: auto !important;
            }
            main {
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-[#f4f6fa] text-slate-800 font-sans antialiased h-screen overflow-hidden flex w-full">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden lg:hidden no-print"></div>

    <!-- Sidebar Eksekutif -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 h-full bg-white border-r border-slate-200/80 z-50 transition-transform duration-200 -translate-x-full lg:translate-x-0 lg:static lg:h-full flex flex-col justify-between shrink-0 no-print">
        <div class="flex-1 overflow-y-auto">
            <!-- Brand Logo -->
            <div class="min-h-20 py-4 px-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <a href="{{ route('eksekutif.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 shadow-xs p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Kominfo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-base font-bold text-slate-900 tracking-tight block">SIMBANGDA</span>
                        <span class="text-[10px] font-semibold tracking-wider text-[#0D47A1] uppercase block">Portal Eksekutif</span>
                    </div>
                </a>
                <button id="closeSidebarBtn" class="lg:hidden text-slate-400 hover:text-slate-700">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <!-- Role Badge Header -->
            <div class="p-3.5 mx-3 my-3 rounded-xl bg-blue-50/70 border border-blue-100">
                <div class="text-[10px] font-bold text-[#0D47A1] uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">verified</span>
                    <span>
                        @if (auth()->user()->hasRole('kepala_dinas'))
                            Pimpinan Tertinggi
                        @else
                            Pimpinan Bidang Teknis
                        @endif
                    </span>
                </div>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ auth()->user()->jabatan ?? (auth()->user()->hasRole('kepala_dinas') ? 'Kepala Dinas Kominfo' : 'Kepala Bidang APTIKA') }}
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1">
                <a href="{{ route('eksekutif.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('eksekutif.dashboard') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">analytics</span>
                    <span>Executive Dashboard</span>
                </a>

                @canany(['project.view', 'eksekutif.penundaan.review', 'master.aplikasi.view'])
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        Monitoring & Kebijakan
                    </div>
                @endcanany

                @can('project.view')
                    <a href="{{ route('eksekutif.project.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('eksekutif.project.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">account_tree</span>
                        <span>Portofolio Proyek</span>
                    </a>
                @endcan

                @can('eksekutif.penundaan.review')
                    @php
                        $pendingPenundaanCount = \App\Models\Penundaan::where('status', 'diajukan')->count();
                    @endphp
                    <a href="{{ route('eksekutif.penundaan.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('eksekutif.penundaan.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                            <span>Persetujuan Kendala</span>
                        </div>
                        @if ($pendingPenundaanCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ request()->routeIs('eksekutif.penundaan.*') ? 'bg-white text-[#0D47A1]' : 'bg-rose-500 text-white' }} animate-pulse">
                                {{ $pendingPenundaanCount }}
                            </span>
                        @endif
                    </a>
                @endcan

                @can('master.aplikasi.view')
                    <a href="{{ route('eksekutif.aplikasi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('eksekutif.aplikasi.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">apps</span>
                        <span>Katalog Aplikasi OPD</span>
                    </a>
                @endcan

                @can('eksekutif.laporan.view')
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        Pelaporan Pimpinan
                    </div>

                    <a href="{{ route('eksekutif.laporan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('eksekutif.laporan.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span>Laporan Eksekutif</span>
                    </a>
                @endcan
            </nav>
        </div>

        <!-- User Profile & Logout Bottom -->
        <div class="p-3 border-t border-slate-100 shrink-0">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-[#0D47A1] font-bold text-xs flex items-center justify-center shrink-0 uppercase border border-blue-200">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ '@' . auth()->user()->username }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Keluar">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 min-w-0 flex flex-col h-full overflow-hidden">
        <!-- Top Header Bar -->
        <header class="min-h-20 py-4 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between shrink-0 z-30 no-print">
            <div class="flex items-center gap-3">
                <button id="openSidebarBtn" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-snug">@yield('page-title', 'Portal Pimpinan Eksekutif')</h1>
                    <div class="text-xs text-slate-400 flex items-center gap-1.5 mt-0.5">
                        <span>Pimpinan</span>
                        <span>/</span>
                        <span class="text-slate-600 font-medium">@yield('breadcrumb', 'Dashboard')</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Executive Indicator Badge -->
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0D47A1] border border-blue-200">
                    <span class="w-2 h-2 rounded-full bg-[#0D47A1]"></span>
                    <span>
                        @if (auth()->user()->hasRole('kepala_dinas'))
                            Kepala Dinas (Eksekutif Makro)
                        @else
                            Kepala Bidang (Manajerial Proyek)
                        @endif
                    </span>
                </span>
            </div>
        </header>

        <!-- Scrollable Content Area -->
        <div class="main-scroll-container flex-1 overflow-y-auto overflow-x-hidden flex flex-col justify-between">
            <div>
                <!-- Page Flash Notifications -->
                @if (session('success'))
                    <div class="mx-4 sm:mx-8 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs no-print">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-4 sm:mx-8 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-xs no-print">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endif

                <!-- Main Body -->
                <main class="flex-1 p-4 sm:p-8">
                    @yield('content')
                </main>
            </div>

            <!-- Footer -->
            <footer class="py-4 px-6 sm:px-8 border-t border-slate-200/80 bg-white text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0 no-print">
                <div>
                    &copy; {{ date('Y') }} Diskominfotik Provinsi Riau &mdash; Sistem Manajemen Aplikasi Daerah (SIMBANGDA)
                </div>
                <div>
                    Portal Pimpinan Eksekutif &bull; Versi 1.0
                </div>
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
    @yield('scripts')
</body>
</html>
