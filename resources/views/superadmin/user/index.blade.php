@extends('layouts.admin')

@section('title', 'Master User: Pengguna')
@section('page-title', 'Manajemen Pengguna (User)')
@section('breadcrumb', 'Master User / User')

@section('content')
<div class="space-y-6">
    <!-- Filter and Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('superadmin.user.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-3xl w-full">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama, username, email..." 
                    class="block w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
                >
            </div>

            <!-- Role Filter -->
            <div class="w-48">
                <select name="role" onchange="this.form.submit()" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer">
                    <option value="">Semua Peran</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ $roleFilter === $r->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Instansi Filter -->
            <div class="w-56">
                <select name="instansi_id" onchange="this.form.submit()" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer truncate">
                    <option value="">Semua Instansi</option>
                    @foreach ($instansis as $ins)
                        <option value="{{ $ins->id }}" {{ $instansiFilter == $ins->id ? 'selected' : '' }}>
                            {{ $ins->nama_instansi }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if ($search || $roleFilter || $instansiFilter)
                <a href="{{ route('superadmin.user.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>

        <!-- Add User Button -->
        <a href="{{ route('superadmin.user.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>Tambah Pengguna</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Pengguna</th>
                        <th class="py-3.5 px-6">Username</th>
                        <th class="py-3.5 px-6">Peran Sistem</th>
                        <th class="py-3.5 px-6">Instansi Terkait</th>
                        <th class="py-3.5 px-6">Terdaftar Sejak</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-[#0D47A1] font-bold text-xs flex items-center justify-center shrink-0 uppercase border border-slate-200/60">
                                        {{ substr($u->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs font-mono text-slate-600">
                                {{ $u->username ?? '-' }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $role = $u->roles->first()?->name;
                                    $badgeColor = match($role) {
                                        'superadmin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'admin_aptika' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'admin_infrastruktur' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'admin_persandian' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'opd' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'kepala_bidang', 'kepala_dinas' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'ketua_tim' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badgeColor }}">
                                    {{ $role ? str_replace('_', ' ', $role) : 'Tanpa Role' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                {{ $u->instansi?->nama_instansi ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400">
                                {{ $u->created_at->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.user.edit', $u) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0D47A1] hover:bg-slate-100 transition" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('superadmin.user.destroy', $u) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">person_off</span>
                                <p class="text-sm font-semibold">Tidak ada pengguna ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
