<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Dashboard') - SIMBANGDA Riau</title>
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0D47A1",
                        "primary-hover": "#165eae",
                        "primary-light": "#e7eeff",
                        "secondary": "#165eae",
                        "surface": "#f8fafc",
                        "surface-dark": "#0f172a",
                    },
                    fontFamily: {
                        "sans": ["Plus Jakarta Sans", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        html, body {
            overflow-x: hidden;
            overscroll-behavior-x: none;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body class="bg-[#f4f6fa] text-slate-800 font-sans antialiased h-screen overflow-hidden flex w-full">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 h-full bg-white border-r border-slate-200/80 z-50 transition-transform duration-200 -translate-x-full lg:translate-x-0 lg:static lg:h-full flex flex-col justify-between shrink-0">
        <div class="flex-1 overflow-y-auto">
            <!-- Sidebar Header -->
            <div class="min-h-20 py-4 px-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 shadow-xs p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Kominfo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-base font-bold text-slate-900 tracking-tight block">SIMBANGDA</span>
                        <span class="text-[10px] font-semibold tracking-wider text-[#0D47A1] uppercase block">Diskominfo Riau</span>
                    </div>
                </a>
                <button id="closeSidebarBtn" class="lg:hidden text-slate-400 hover:text-slate-700">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>Dashboard</span>
                </a>

                @php
                    $isMasterUserActive = request()->routeIs('superadmin.user.*', 'superadmin.role.*', 'superadmin.permission.*');
                @endphp

                <!-- Master User (Menu Dropdown Collapsible) -->
                <div class="pt-1">
                    <button type="button" id="masterUserToggleBtn" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer select-none {{ $isMasterUserActive ? 'bg-blue-50 text-[#0D47A1]' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px] {{ $isMasterUserActive ? 'text-[#0D47A1]' : 'text-slate-500' }}">manage_accounts</span>
                            <span>Master User</span>
                        </div>
                        <span class="material-symbols-outlined text-[18px] transition-transform duration-200 {{ $isMasterUserActive ? 'rotate-180' : '' }}" id="masterUserChevron">expand_more</span>
                    </button>

                    <div id="masterUserSubmenu" class="mt-1 ml-4 pl-3 border-l-2 border-slate-200/80 space-y-1 {{ $isMasterUserActive ? '' : 'hidden' }}">
                        <a href="{{ route('superadmin.user.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('superadmin.user.*') ? 'bg-[#0D47A1] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                            <span>User</span>
                        </a>

                        <a href="{{ route('superadmin.role.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition {{ (request()->routeIs('superadmin.role.*') && !request()->routeIs('superadmin.role.permissions*')) ? 'bg-[#0D47A1] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                            <span>Role</span>
                        </a>

                        <a href="{{ route('superadmin.permission.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition {{ (request()->routeIs('superadmin.permission.*') || request()->routeIs('superadmin.role.permissions*')) ? 'bg-[#0D47A1] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                            <span class="material-symbols-outlined text-[18px]">vpn_key</span>
                            <span>Permission</span>
                        </a>
                    </div>
                </div>

                <div class="pt-4 pb-2 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                    Master Data
                </div>

                <a href="{{ route('superadmin.instansi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('superadmin.instansi.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">domain</span>
                    <span>Instansi OPD</span>
                </a>

                <a href="{{ route('superadmin.anggota-tim.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('superadmin.anggota-tim.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                    <span>Data Anggota Tim</span>
                </a>

                <a href="{{ route('superadmin.aplikasi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('superadmin.aplikasi.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">apps</span>
                    <span>Master Aplikasi</span>
                </a>

                <a href="{{ route('superadmin.master-dokumen.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('superadmin.master-dokumen.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">folder_special</span>
                    <span>Master Dokumen</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer (User Info & Logout) -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-[#0D47A1] text-white flex items-center justify-center font-bold text-sm shrink-0 uppercase shadow-xs">
                    {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-[#0D47A1] uppercase tracking-wider">
                        {{ auth()->user()->roles->first()->name ?? 'Role' }}
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
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">@yield('page-title', 'SIMBANGDA Panel')</h1>
                    <div class="text-xs text-slate-500 hidden sm:block mt-0.5">
                        @yield('breadcrumb', 'Pemerintah Provinsi Riau')
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200/70 text-xs text-slate-600">
                    <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">calendar_today</span>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </header>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden flex flex-col justify-between">
            <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
                <!-- Global Flash Alerts -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200/90 text-emerald-800 text-sm flex items-start justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[22px] text-emerald-600 shrink-0">check_circle</span>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/90 text-rose-800 text-sm flex items-start justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[22px] text-rose-600 shrink-0">error</span>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="py-4 px-8 border-t border-slate-200/80 bg-white text-center sm:text-left text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
                <span>&copy; {{ date('Y') }} Dinas Komunikasi, Informatika &amp; Statistik Provinsi Riau.</span>
                <span>Versi 1.0.0</span>
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

        // Master User Accordion Toggle
        const masterUserBtn = document.getElementById('masterUserToggleBtn');
        const masterUserSubmenu = document.getElementById('masterUserSubmenu');
        const masterUserChevron = document.getElementById('masterUserChevron');

        if (masterUserBtn && masterUserSubmenu) {
            masterUserBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const isHidden = masterUserSubmenu.classList.contains('hidden');
                if (isHidden) {
                    masterUserSubmenu.classList.remove('hidden');
                    if (masterUserChevron) masterUserChevron.classList.add('rotate-180');
                } else {
                    masterUserSubmenu.classList.add('hidden');
                    if (masterUserChevron) masterUserChevron.classList.remove('rotate-180');
                }
            });
        }
    </script>
</body>
</html>
