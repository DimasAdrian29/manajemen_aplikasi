@extends('layouts.admin')

@section('title', 'Master User: Permission')
@section('page-title', 'Manajemen Hak Akses & Permission')
@section('breadcrumb', 'Master User / Permission')

@section('content')
<div class="space-y-6">
    <!-- Header Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">admin_panel_settings</span>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-500">Total Peran</div>
                <div class="text-sm sm:text-base font-bold text-slate-800 leading-tight mt-0.5">{{ $roles->count() }} Peran</div>
            </div>
        </div>

        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">view_module</span>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-500">Modul Sistem</div>
                <div class="text-sm sm:text-base font-bold text-slate-800 leading-tight mt-0.5">10 Poin Modul</div>
            </div>
        </div>

        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">vpn_key</span>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-500">Katalog Permission</div>
                <div class="text-sm sm:text-base font-bold text-slate-800 leading-tight mt-0.5">{{ $totalPermissions }} Izin</div>
            </div>
        </div>

        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">verified_user</span>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-500">Status Proteksi</div>
                <div class="text-sm sm:text-base font-bold text-slate-800 leading-tight mt-0.5">Spatie RBAC</div>
            </div>
        </div>
    </div>

    <!-- Intro Card -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">tune</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Peran dan Hak Akses Sistem</h2>
                <p class="text-xs text-slate-500 mt-0.5">Klik tombol <strong>Atur Permission</strong> pada baris peran untuk membuka konfigurasi izin modul (CRUD & fungsi teknis).</p>
            </div>
        </div>
        <div class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-3.5 py-2 rounded-xl flex items-center gap-2 self-start md:self-auto">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Real-time Gate Permission Cache</span>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">No</th>
                        <th class="py-3.5 px-6">Identitas Peran (Role)</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6 text-center">Pengguna Aktif</th>
                        <th class="py-3.5 px-6 text-center">Cakupan Izin Aktif</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($roles as $index => $item)
                        @php
                            $isSuperadmin = $item->name === 'superadmin';
                            $isSystemRole = in_array($item->name, $systemRolesList, true);
                            $percentage = $totalPermissions > 0 ? round(($item->permissions_count / $totalPermissions) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 text-slate-400 font-mono text-xs">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $isSuperadmin ? 'bg-amber-100 text-amber-800' : ($isSystemRole ? 'bg-blue-100 text-[#0D47A1]' : 'bg-slate-100 text-slate-700') }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">
                                            {{ $isSuperadmin ? 'stars' : ($isSystemRole ? 'shield' : 'group') }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 leading-tight">
                                            {{ ucwords(str_replace('_', ' ', $item->name)) }}
                                        </div>
                                        <div class="font-mono text-[11px] text-slate-400 mt-0.5">
                                            {{ $item->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($isSuperadmin)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Superadmin Utama
                                    </span>
                                @elseif ($isSystemRole)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0D47A1] border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#0D47A1]"></span>
                                        Sistem Bawaan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Peran Kustom
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $item->users_count > 0 ? 'bg-slate-100 text-slate-800' : 'bg-slate-50 text-slate-400' }}">
                                    <span class="material-symbols-outlined text-[16px]">person</span>
                                    <span>{{ $item->users_count }} Pengguna</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($isSuperadmin)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        <span class="material-symbols-outlined text-[16px] text-amber-700">all_inclusive</span>
                                        <span>Akses Penuh (10 Modul)</span>
                                    </span>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->permissions_count > 0 ? 'bg-blue-50 text-[#0D47A1] border border-blue-200' : 'bg-slate-100 text-slate-500' }}">
                                            <span class="material-symbols-outlined text-[14px]">vpn_key</span>
                                            <span>{{ $item->permissions_count }} / {{ $totalPermissions }} Izin</span>
                                        </span>
                                        <div class="w-24 bg-slate-200 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                            <div class="bg-[#0D47A1] h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('superadmin.role.permissions', $item) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-[#0D47A1] hover:bg-[#165eae] shadow-sm shadow-[#0D47A1]/20 transition cursor-pointer" title="Buka pengaturan modul untuk peran {{ ucwords(str_replace('_', ' ', $item->name)) }}">
                                    <span class="material-symbols-outlined text-[16px]">tune</span>
                                    <span>Atur Permission</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[48px] block mb-2 text-slate-300">shield_person</span>
                                Belum ada data peran dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Guide Info Card -->
    <div class="p-5 rounded-2xl bg-blue-50/60 border border-blue-100 flex items-start gap-4 text-xs text-blue-900">
        <span class="material-symbols-outlined text-blue-700 text-[24px] shrink-0 mt-0.5">info</span>
        <div class="space-y-1 leading-relaxed">
            <h4 class="font-bold text-sm text-blue-950">Panduan Pengaturan Hak Akses Peran</h4>
            <p>1. Klik tombol <strong>Atur Permission</strong> pada peran yang ingin Anda kelola izinnya.</p>
            <p>2. Pada halaman pengaturan modul, Anda dapat mencentang izin CRUD (*View Any, View, Create, Update, Delete*) serta aksi khusus per modul.</p>
            <p>3. Tekan tombol <strong>Simpan Perubahan Hak Akses</strong> untuk menerapkan otorisasi sistem Spatie RBAC secara langsung.</p>
        </div>
    </div>
</div>
@endsection
