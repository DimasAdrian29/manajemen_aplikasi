@extends('layouts.aptika')

@section('title', 'Agenda Rapat Proyek')
@section('page-title', 'Agenda & Koordinasi Rapat Proyek')
@section('breadcrumb', 'Kendali Mutu / Agenda Rapat')

@section('content')
<div class="space-y-6">
    <!-- Action Bar & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('aptika.rapat.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-xl">
            <select 
                name="project_id" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
            >
                <option value="">Semua Proyek Aplikasi</option>
                @foreach ($projects as $proj)
                    <option value="{{ $proj->id }}" {{ $projectId == $proj->id ? 'selected' : '' }}>
                        {{ $proj->nama_project }}
                    </option>
                @endforeach
            </select>

            <select 
                name="status" 
                onchange="this.form.submit()" 
                class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] shadow-xs cursor-pointer"
            >
                <option value="">Semua Status Rapat</option>
                <option value="dijadwalkan" {{ $status === 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai Diselenggarakan</option>
                <option value="dibatalkan" {{ $status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            @if ($projectId || $status)
                <a href="{{ route('aptika.rapat.index') }}" class="px-2 text-xs text-slate-500 hover:text-slate-800 font-semibold">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('aptika.rapat.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-xs font-bold shadow-sm shadow-[#0D47A1]/20 transition shrink-0">
            <span class="material-symbols-outlined text-[18px]">add_call</span>
            <span>Jadwalkan Rapat Baru</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Waktu & Tanggal</th>
                        <th class="py-3.5 px-6">Nama Rapat & Agenda</th>
                        <th class="py-3.5 px-6">Proyek & Instansi</th>
                        <th class="py-3.5 px-6">Tempat</th>
                        <th class="py-3.5 px-6 text-center">Peserta</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($rapats as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 text-xs whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $item->waktu_rapat->format('d M Y') }}</div>
                                <div class="text-slate-400 font-mono">{{ $item->waktu_rapat->format('H:i') }} WIB</div>
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('aptika.rapat.show', $item) }}" class="font-bold text-slate-900 hover:text-[#0D47A1] transition">
                                    {{ $item->nama_rapat }}
                                </a>
                                @if ($item->deskripsi)
                                    <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5" title="{{ $item->deskripsi }}">
                                        {{ $item->deskripsi }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <div class="font-semibold text-slate-800">{{ $item->project->nama_project }}</div>
                                <div class="text-slate-400">{{ $item->project->aplikasi->instansi->nama_instansi ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                {{ $item->tempat_rapat ?? 'Ruang Rapat APTIKA' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    <span class="material-symbols-outlined text-[13px]">groups</span>
                                    <span>{{ $item->peserta->count() }} orang</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($item->status === 'dijadwalkan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Dijadwalkan</span>
                                    </span>
                                @elseif ($item->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Selesai</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Dibatalkan</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <a href="{{ route('aptika.rapat.show', $item) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-[#0D47A1] bg-blue-50 hover:bg-blue-100 transition">
                                    <span>Lembar Rapat</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-2">event_busy</span>
                                <p class="text-sm font-semibold">Belum ada agenda rapat yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rapats->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $rapats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
