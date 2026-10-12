@extends('layouts.admin')

@section('title', 'Master Anggota Tim Pengembang')
@section('page-title', 'Data Anggota Tim Pengembang')
@section('breadcrumb', 'Master Data / Personil & Tim Proyek')

@section('content')
<div class="space-y-6">
    <!-- Filter and Action Bar -->
    <!-- Filter and Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('superadmin.anggota-tim.index') }}" class="flex items-center gap-2.5 max-w-md w-full">
            <!-- Search Input -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama, keahlian, no HP..." 
                    class="block w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs"
                >
            </div>

            <button type="submit" class="px-4 py-2.5 bg-[#0D47A1] text-white text-xs font-bold rounded-xl hover:bg-[#1565C0] transition flex items-center gap-1.5 shadow-xs shrink-0 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">search</span>
                <span>Cari</span>
            </button>

            @if ($search)
                <a href="{{ route('superadmin.anggota-tim.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 text-xs font-semibold shrink-0">
                    Reset
                </a>
            @endif
        </form>

        <!-- Add Button -->
        <a href="{{ route('superadmin.anggota-tim.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>Tambah Personil</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Personil Pengembang</th>
                        <th class="py-3.5 px-6">Kontak &amp; Akun</th>
                        <th class="py-3.5 px-6">Keahlian / Posisi Teknis</th>
                        <th class="py-3.5 px-6 text-center">Riwayat Peran Proyek</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($anggotaTims as $person)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0D47A1] font-bold text-sm flex items-center justify-center shrink-0 uppercase border border-blue-100">
                                        {{ substr($person->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $person->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $person->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <div class="font-mono text-slate-700 font-semibold">{{ '@' . $person->username }}</div>
                                <div class="text-slate-500 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">call</span>
                                    <span>{{ $person->no_hp ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($person->jabatan)
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $person->jabatan }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Tenaga Teknis</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        <span class="material-symbols-outlined text-[14px]">folder</span>
                                        <span>{{ $person->tim_projects_count }} Proyek</span>
                                    </span>
                                    @if ($person->tim_projects_count > 0)
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <span class="text-indigo-700 font-semibold" title="Pernah ditunjuk sebagai Ketua Tim">{{ $person->ketua_count ?? 0 }}x Ketua</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-slate-600 font-medium" title="Pernah ditugaskan sebagai Anggota Tim">{{ $person->anggota_count ?? 0 }}x Anggota</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Belum ada penugasan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.anggota-tim.edit', $person) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0D47A1] hover:bg-slate-100 transition" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.anggota-tim.destroy', $person) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus personil {{ $person->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">engineering</span>
                                <p class="text-sm font-semibold">Belum ada data anggota tim pengembang.</p>
                                <p class="text-xs text-slate-400 mt-1">Daftarkan personil teknis (Programmer, Analis, Desainer, dll) agar siap ditugaskan ke proyek.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($anggotaTims->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $anggotaTims->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
