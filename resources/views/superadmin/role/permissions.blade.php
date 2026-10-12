@extends('layouts.admin')

@section('title', 'Master User: Permission — ' . ucwords(str_replace('_', ' ', $role->name)))
@section('page-title', 'Pengaturan Hak Akses Peran (Permission)')
@section('breadcrumb', 'Master User / Permission / ' . ucwords(str_replace('_', ' ', $role->name)))

@section('content')
<div class="space-y-4">
    <!-- Header Informasi Peran (Ramping & Efisien) -->
    <div class="px-5 py-3 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3.5">
        <div class="w-9 h-9 rounded-xl {{ $isSuperadmin ? 'bg-amber-100 text-amber-800' : 'bg-blue-50 text-[#0D47A1]' }} flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[20px]">
                {{ $isSuperadmin ? 'stars' : 'tune' }}
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-900 leading-tight">
                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                </h2>
                <span class="font-mono text-[11px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">
                    {{ $role->name }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Konfigurasi izin per modul (CRUD & Aksi Spesifik) untuk peran ini.
            </p>
        </div>
    </div>

    @if ($isSuperadmin)
        <!-- Superadmin Notice -->
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3.5 shadow-xs">
            <span class="material-symbols-outlined text-amber-600 text-[26px] shrink-0 mt-0.5">info</span>
            <div>
                <h4 class="font-bold text-sm">Peran Superadmin Memiliki Hak Akses Penuh (Global Gate Bypass)</h4>
                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                    Peran <strong>superadmin</strong> adalah peran induk pengelola sistem dan secara permanen memiliki akses mutlak ke seluruh modul dan operasi sistem tanpa batasan izin individual.
                </p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.role.permissions.update', $role) }}" id="formPermissionsMatrix">
        @csrf
        @method('PUT')

        <!-- Control Bar (Select All / Deselect All) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-slate-100/80 border border-slate-200/70 mb-6">
            <div class="text-xs font-bold text-slate-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#0D47A1]">checklist</span>
                <span>Matriks 10 Poin Modul Sistem</span>
                <span class="px-2 py-0.5 rounded-full bg-white text-slate-700 text-[11px] font-bold border border-slate-200" id="totalActivePermissionsCount">
                    {{ count($rolePermissions) }} Izin Terpilih
                </span>
            </div>

            @if (!$isSuperadmin)
                <div class="flex items-center gap-2">
                    <button type="button" onclick="selectAllGlobal(true)" class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold shadow-2xs transition cursor-pointer">
                        Centang Semua (100%)
                    </button>
                    <button type="button" onclick="selectAllGlobal(false)" class="px-3 py-1.5 rounded-lg bg-white hover:bg-rose-50 text-rose-700 border border-rose-200 text-xs font-semibold shadow-2xs transition cursor-pointer">
                        Kosongkan Semua (0%)
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-xs transition cursor-pointer ml-1">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        <span>Simpan</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- 10 Modules Multi-Column Card Grid (3-4 Modul Per Baris) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-stretch">
            @foreach ($modulesMeta as $moduleKey => $meta)
                @php
                    $modulePermissions = $permissionsByModule->get($moduleKey, collect());
                    $modulePermNames = $modulePermissions->pluck('name')->toArray();
                    $activeInModule = count(array_intersect($modulePermNames, $rolePermissions));
                    $totalInModule = $modulePermissions->count();
                @endphp

                <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#0D47A1]/30 transition flex flex-col h-full module-card overflow-hidden" data-module="{{ $moduleKey }}">
                    <!-- Module Header -->
                    <div class="p-3.5 bg-slate-50/80 border-b border-slate-100 flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-[#0D47A1]/10 text-[#0D47A1] flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">{{ $meta['icon'] }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-900 text-xs leading-snug line-clamp-1" title="{{ $meta['name'] }}">
                                        {{ $meta['name'] }}
                                    </h3>
                                    <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5" title="{{ $meta['description'] }}">
                                        {{ $meta['description'] }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white border border-slate-200 text-slate-700 shrink-0 module-badge" data-module-badge="{{ $moduleKey }}">
                                {{ $activeInModule }}/{{ $totalInModule }}
                            </span>
                        </div>

                        @if (!$isSuperadmin)
                            <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-slate-200/60 text-[10px]">
                                <button type="button" onclick="toggleModuleGroup('{{ $moduleKey }}', true)" class="px-2 py-0.5 rounded-md font-semibold text-[#0D47A1] hover:bg-blue-100/60 transition cursor-pointer">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300">&bull;</span>
                                <button type="button" onclick="toggleModuleGroup('{{ $moduleKey }}', false)" class="px-2 py-0.5 rounded-md font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                    Kosongkan
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Permissions List (Compact Scrollable Container) -->
                    <div class="p-3 flex-1 overflow-y-auto max-h-72 space-y-1.5 scrollbar-thin">
                        @foreach ($modulePermissions as $perm)
                            @php
                                $isChecked = in_array($perm->name, $rolePermissions, true) || $isSuperadmin;
                                
                                $actionTag = 'KHUSUS';
                                $tagClass = 'bg-purple-50 text-purple-700 border-purple-200';
                                
                                if (str_ends_with($perm->name, '.view') || str_contains($perm->name, '.read')) {
                                    $actionTag = 'VIEW';
                                    $tagClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                } elseif (str_ends_with($perm->name, '.create')) {
                                    $actionTag = 'CREATE';
                                    $tagClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } elseif (str_ends_with($perm->name, '.edit') || str_ends_with($perm->name, '.update')) {
                                    $actionTag = 'UPDATE';
                                    $tagClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                } elseif (str_ends_with($perm->name, '.delete')) {
                                    $actionTag = 'DELETE';
                                    $tagClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                } elseif (str_ends_with($perm->name, '.review')) {
                                    $actionTag = 'REVIEW';
                                    $tagClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                }
                            @endphp

                            <label class="flex items-start gap-2 p-2 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50/70 transition cursor-pointer select-none {{ $isChecked ? 'bg-blue-50/25 border-blue-100' : 'bg-white' }}">
                                <input 
                                    type="checkbox" 
                                    name="permissions[]" 
                                    value="{{ $perm->name }}" 
                                    data-module="{{ $moduleKey }}"
                                    {{ $isChecked ? 'checked' : '' }}
                                    {{ $isSuperadmin ? 'disabled' : '' }}
                                    onchange="updateCounts()"
                                    class="mt-0.5 w-3.5 h-3.5 rounded text-[#0D47A1] focus:ring-[#0D47A1] border-slate-300 shrink-0"
                                >
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                        <span class="inline-block px-1 py-0.2 rounded text-[9px] font-bold border {{ $tagClass }}">
                                            {{ $actionTag }}
                                        </span>
                                        <span class="font-mono text-[9px] text-slate-400 truncate" title="{{ $perm->name }}">
                                            {{ $perm->name }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] font-medium text-slate-900 leading-snug">
                                        {{ $perm->display_name ?? $perm->name }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @if (!$isSuperadmin)
            <!-- Bottom Sticky Action Bar -->
            <div class="sticky bottom-4 z-20 mt-8 p-4 rounded-2xl bg-white border border-slate-200 shadow-xl flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                    <span class="material-symbols-outlined text-[20px] text-[#0D47A1]">shield</span>
                    <span>Pastikan hak akses yang diberikan sesuai dengan tupoksi peran terkait.</span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('superadmin.permission.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-md shadow-[#0D47A1]/25 transition cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Perubahan Hak Akses</span>
                    </button>
                </div>
            </div>
        @endif
    </form>
</div>

<script>
    function toggleModuleGroup(moduleKey, check) {
        const checkboxes = document.querySelectorAll(`input[data-module="${moduleKey}"]`);
        checkboxes.forEach(cb => {
            if (!cb.disabled) {
                cb.checked = check;
            }
        });
        updateCounts();
    }

    function selectAllGlobal(check) {
        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
        checkboxes.forEach(cb => {
            if (!cb.disabled) {
                cb.checked = check;
            }
        });
        updateCounts();
    }

    function updateCounts() {
        let totalChecked = 0;
        const modules = {};

        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
            const mod = cb.getAttribute('data-module');
            if (!modules[mod]) {
                modules[mod] = { total: 0, checked: 0 };
            }
            modules[mod].total++;
            if (cb.checked) {
                totalChecked++;
                modules[mod].checked++;
            }
        });

        const totalCounter = document.getElementById('totalActivePermissionsCount');
        if (totalCounter) {
            totalCounter.textContent = `${totalChecked} Izin Terpilih`;
        }

        for (const [modKey, stats] of Object.entries(modules)) {
            const badge = document.querySelector(`[data-module-badge="${modKey}"]`);
            if (badge) {
                badge.textContent = `${stats.checked} / ${stats.total} Aktif`;
            }
        }
    }
</script>
@endsection
