<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Persandian') - SIMBANGDA Riau</title>

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

    <!-- Sidebar Persandian -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 h-full bg-white border-r border-slate-200/80 z-50 transition-transform duration-200 -translate-x-full lg:translate-x-0 lg:static lg:h-full flex flex-col justify-between shrink-0">
        <div class="flex-1 overflow-y-auto">
            <!-- Brand Logo -->
            <div class="min-h-20 py-4 px-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <a href="{{ route('persandian.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 shadow-xs p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Kominfo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-base font-bold text-slate-900 tracking-tight block">SIMBANGDA</span>
                        <span class="text-[10px] font-semibold tracking-wider text-[#0D47A1] uppercase block">Bidang Persandian</span>
                    </div>
                </a>
                <button id="closeSidebarBtn" class="lg:hidden text-slate-400 hover:text-slate-700">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <!-- Role Badge -->
            <div class="p-3.5 mx-3 my-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Persandian & Keamanan Informasi</div>
                <div class="text-xs font-semibold text-slate-700 mt-0.5">
                    CSIRT & Audit Kerentanan Siber
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1">
                <a href="{{ route('persandian.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('persandian.dashboard') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('workflow.task.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('workflow.task.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                    <span>Tugas Alur Saya</span>
                </a>

                @can('security.audit.view')
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        Audit Keamanan Siber
                    </div>

                    @php
                        $pendingSecurityCount = \App\Models\Project::where('status', 'security_testing')->count();
                    @endphp

                    <a href="{{ route('persandian.pengujian.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('persandian.pengujian.index') || request()->routeIs('persandian.pengujian.create') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">security</span>
                            <span>Antrean Uji Keamanan</span>
                        </div>
                        @if ($pendingSecurityCount > 0)
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ request()->routeIs('persandian.pengujian.index') || request()->routeIs('persandian.pengujian.create') ? 'bg-white text-[#0D47A1]' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $pendingSecurityCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('persandian.pengujian.riwayat') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('persandian.pengujian.riwayat') || request()->routeIs('persandian.pengujian.show') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
                        <span class="material-symbols-outlined text-[20px]">folder_shared</span>
                        <span>Arsip & Riwayat Audit</span>
                    </a>
                @endcan
            </nav>
        </div>

        <!-- User Profile Card in Sidebar -->
        <div class="p-3 border-t border-slate-100 bg-slate-50/50 shrink-0">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-white border border-slate-200/80">
                <div class="w-9 h-9 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'SAN', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800 truncate leading-snug">{{ auth()->user()->name ?? 'Admin Persandian' }}</p>
                    <p class="text-[11px] font-medium text-slate-500 truncate leading-snug">{{ auth()->user()->email ?? 'persandian@riau.go.id' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 min-w-0 flex flex-col h-full overflow-hidden">
        <!-- Topbar -->
        <header class="min-h-20 py-4 bg-white border-b border-slate-200/80 px-4 lg:px-8 flex items-center justify-between shrink-0 z-30">
            <div class="flex items-center gap-3">
                <button id="openSidebarBtn" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                <div>
                    <h1 class="text-lg lg:text-xl font-bold text-slate-900 leading-snug">@yield('page_title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-500 hidden sm:block mt-0.5">@yield('page_subtitle', 'Portal Pengujian Kerentanan & Audit Keamanan Informasi Pemerintah Provinsi Riau')</p>
                </div>
            </div>

            <!-- Topbar Actions -->
            <div class="flex items-center gap-3">
                <!-- CSIRT Status Badge -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    CSIRT Riau Active
                </div>

                <div class="h-6 w-px bg-slate-200 hidden md:block"></div>

                <a href="{{ route('persandian.pengujian.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] transition shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">security</span>
                    <span>Audit Baru</span>
                </a>
            </div>
        </header>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden flex flex-col justify-between">
            <div>
                <!-- Flash Messages -->
                <div class="px-4 lg:px-8 pt-4">
                    @if (session('success'))
                        <div class="flex items-center gap-3 p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                            <span class="material-symbols-outlined text-emerald-600 text-[22px]">check_circle</span>
                            <div class="flex-1 font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="flex items-center gap-3 p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                            <span class="material-symbols-outlined text-rose-600 text-[22px]">error</span>
                            <div class="flex-1 font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="flex items-center gap-3 p-4 mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
                            <span class="material-symbols-outlined text-amber-600 text-[22px]">warning</span>
                            <div class="flex-1 font-medium">{{ session('warning') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                            <div class="flex items-center gap-2 font-bold mb-1">
                                <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
                                Terdapat kesalahan pada input form:
                            </div>
                            <ul class="list-disc list-inside text-xs space-y-0.5 ml-6">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Page Body -->
                <main class="flex-1 p-4 lg:p-8">
                    @yield('content')
                </main>
            </div>

            <!-- Footer -->
            <footer class="border-t border-slate-200/80 bg-white px-4 lg:px-8 py-4 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
                <div>&copy; {{ date('Y') }} Dinas Komunikasi, Informatika dan Statistik Provinsi Riau.</div>
                <div class="font-medium text-slate-400">Bidang Persandian & Keamanan Informasi (CSIRT Riau)</div>
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

    @stack('scripts')
</body>
</html>
