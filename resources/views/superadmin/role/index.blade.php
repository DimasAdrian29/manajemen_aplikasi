@extends('layouts.admin')

@section('title', 'Master User: Role')
@section('page-title', 'Kelola Role (Peran Pengguna)')
@section('breadcrumb', 'Master User / Role')

@section('content')
@php
    $systemRolesList = ['superadmin', 'admin_aptika', 'admin_infrastruktur', 'admin_persandian', 'opd', 'kepala_bidang', 'kepala_dinas', 'ketua_tim', 'anggota_tim'];
    $systemCount = $roles->filter(fn($r) => in_array($r->name, $systemRolesList, true))->count();
    $customCount = $roles->filter(fn($r) => !in_array($r->name, $systemRolesList, true))->count();
@endphp

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
                <span class="material-symbols-outlined text-[19px]">verified_user</span>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-500">Peran Bawaan</div>
                <div class="text-sm sm:text-base font-bold text-slate-800 leading-tight mt-0.5">{{ $systemCount }} Peran</div>
            </div>
        </div>

        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">extension</span>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-500">Peran Kustom</div>
                <div class="text-sm sm:text-base font-bold text-slate-800 leading-tight mt-0.5">{{ $customCount }} Peran</div>
            </div>
        </div>

        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">group</span>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-500">Total Pengguna</div>
                <div class="text-sm sm:text-base font-bold text-slate-800 leading-tight mt-0.5">{{ \App\Models\User::count() }} Pengguna</div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900">Daftar Peran Pengguna (Role)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola dan tambah peran pengguna (Role) untuk sistem.</p>
        </div>

        <div>
            <button type="button" onclick="openModalTambahRole()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Tambah Peran Baru</span>
            </button>
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
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($roles as $index => $item)
                        @php
                            $isSuperadmin = $item->name === 'superadmin';
                            $isSystemRole = in_array($item->name, $systemRolesList, true);
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
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if (!$isSuperadmin)
                                        <!-- Edit Role Name -->
                                        <button type="button" onclick="openModalEditRole({{ $item->id }}, '{{ $item->name }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-[#0D47A1] hover:bg-slate-100 border border-slate-200 transition cursor-pointer" title="Ubah Nama Peran">
                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Delete Role -->
                                        <form method="POST" action="{{ route('superadmin.role.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran {{ ucwords(str_replace('_', ' ', $item->name)) }} ({{ $item->name }})?{{ $item->users_count > 0 ? ' Peran ini sedang digunakan oleh ' . $item->users_count . ' pengguna dan akan dilepas otomatis.' : '' }}');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 hover:border-rose-600 transition cursor-pointer shadow-2xs" title="Hapus Peran">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200" title="Peran Superadmin dilindungi sistem">
                                            <span class="material-symbols-outlined text-[14px]">lock</span>
                                            <span>Terkunci</span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[48px] block mb-2 text-slate-300">shield_person</span>
                                Belum ada data peran dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Role Baru -->
<div id="modalTambahRole" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">add_moderator</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Tambah Peran Baru</h3>
                    <p class="text-xs text-slate-400">Buat role baru untuk otorisasi akses sistem</p>
                </div>
            </div>
            <button type="button" onclick="closeModalTambahRole()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('superadmin.role.store') }}" class="p-5 space-y-4">
            @csrf
            <div>
                <label for="nama_role_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Peran / Jabatan Baru <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="nama_role_input" 
                    name="name" 
                    required 
                    placeholder="Contoh: auditor_eksternal atau verifikator_keuangan" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                <p class="text-[11px] text-slate-400 mt-1">Nama peran akan otomatis diubah menjadi format slug/snake_case.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalTambahRole()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-[#0D47A1] hover:bg-[#165eae] shadow-xs transition cursor-pointer">
                    Simpan Peran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Role -->
<div id="modalEditRole" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Ubah Nama Peran</h3>
                    <p class="text-xs text-slate-400">Edit identitas nama peran kustom</p>
                </div>
            </div>
            <button type="button" onclick="closeModalEditRole()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="formEditRole" method="POST" action="" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_role_name_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Peran <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="edit_role_name_input" 
                    name="name" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalEditRole()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-[#0D47A1] hover:bg-[#165eae] shadow-xs transition cursor-pointer">
                    Perbarui Peran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahRole() {
        document.getElementById('modalTambahRole').classList.remove('hidden');
    }
    function closeModalTambahRole() {
        document.getElementById('modalTambahRole').classList.add('hidden');
    }
    function openModalEditRole(id, name) {
        const form = document.getElementById('formEditRole');
        form.action = `/superadmin/role/${id}`;
        document.getElementById('edit_role_name_input').value = name;
        document.getElementById('modalEditRole').classList.remove('hidden');
    }
    function closeModalEditRole() {
        document.getElementById('modalEditRole').classList.add('hidden');
    }
</script>
@endsection
