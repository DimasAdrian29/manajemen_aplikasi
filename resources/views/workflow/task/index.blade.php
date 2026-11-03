@extends(auth()->user()->userLayout())

@section('title', 'Antrean Tugas Alur Kerja')
@section('page-title', 'Antrean Tugas Alur')
@section('breadcrumb', 'Alur Kerja / Antrean Tugas')

@section('content')
<div class="space-y-6">

    <!-- Header & Info Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0D47A1] text-2xl">task_alt</span>
                <h2 class="text-base font-bold text-slate-900">Antrean Tugas Alur Kerja Terpadu</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Daftar tahapan alur yang menunggu tindakan dari peran Anda 
                <span class="font-semibold text-slate-700">({{ auth()->user()->roles->pluck('name')->implode(', ') }})</span>.
                Setiap tugas langsung terhubung ke modul resmi penanganannya.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($isSuperadmin)
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                    <span>Mode Superadmin (Semua Antrean)</span>
                </div>
            @endif
            <a href="{{ route('workflow.monitoring.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <span class="material-symbols-outlined text-[16px]">timeline</span>
                <span>Peta Alur Workflow</span>
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('workflow.task.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari berdasarkan nama proyek, nomor pengajuan, nama aplikasi, instansi, atau tahapan alur..." 
                    class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>
            @if(request('search'))
                <a href="{{ route('workflow.task.index') }}" class="px-3.5 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-xl transition">
                    Reset
                </a>
            @endif
            <button type="submit" class="px-4 py-2 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] transition">
                Cari
            </button>
        </form>
    </div>

    <!-- SEKSI 1: Antrean Berkas Permohonan Masuk (Telaah Kelayakan & Inisiasi Proyek) -->
    @if(isset($pengajuans) && $pengajuans->isNotEmpty())
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600 text-xl">move_to_inbox</span>
                    <h3 class="text-sm font-bold text-slate-900">Antrean Berkas Permohonan Masuk (Telaah & Inisiasi)</h3>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                        {{ $pengajuans->count() }} Berkas
                    </span>
                </div>
                <span class="text-[11px] text-slate-400">Berkas masuk dari OPD yang menunggu verifikasi awal</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-amber-50/50 border-b border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                <th class="py-3 px-6">Berkas Permohonan & Instansi</th>
                                <th class="py-3 px-6">Layanan Terkait</th>
                                <th class="py-3 px-6">Status Berkas</th>
                                <th class="py-3 px-6">Menu Penanganan</th>
                                <th class="py-3 px-6 text-right">Tindakan Langsung</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($pengajuans as $pengajuan)
                                @php
                                    $meta = \App\Http\Controllers\Workflow\WorkflowTaskController::resolveTaskMeta($pengajuan);
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                                <span class="material-symbols-outlined text-[18px]">description</span>
                                            </div>
                                            <div>
                                                <span class="text-xs font-bold text-slate-900 block">
                                                    {{ $pengajuan->nama_aplikasi }}
                                                </span>
                                                <span class="text-[11px] text-slate-400 block mt-0.5">
                                                    {{ $pengajuan->instansi?->nama_instansi ?? 'OPD Pemohon' }} • <span class="font-mono text-slate-500 font-semibold">{{ $pengajuan->kode_pengajuan }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        @if($pengajuan->layanan)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: {{ $pengajuan->layanan->warna }}15; color: {{ $pengajuan->layanan->warna }};">
                                                {{ $pengajuan->layanan->nama }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6">
                                        @if($pengajuan->status === 'submitted')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu Telaah
                                            </span>
                                        @elseif($pengajuan->status === 'under_review')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                Sedang Ditelaah
                                            </span>
                                        @elseif($pengajuan->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="material-symbols-outlined text-[13px]">check</span>
                                                Disetujui (Siap Inisiasi)
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-500">{{ ucfirst($pengajuan->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                            <span class="material-symbols-outlined text-[15px] text-slate-500">{{ $meta['icon'] }}</span>
                                            <span>{{ $meta['menu'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <a href="{{ $meta['url'] }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] shadow-xs transition">
                                            <span>{{ $meta['label'] }}</span>
                                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- SEKSI 2: Antrean Tahapan Proyek Berjalan -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0D47A1] text-xl">folder_managed</span>
                <h3 class="text-sm font-bold text-slate-900">Antrean Tahapan Proyek Berjalan (Project Lifecycle)</h3>
                @if($projects->total() > 0)
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                        {{ $projects->total() }} Proyek
                    </span>
                @endif
            </div>
            <span class="text-[11px] text-slate-400">Tahapan verifikasi, pengerjaan, atau pengujian yang sedang aktif</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Proyek & Instansi</th>
                            <th class="py-3.5 px-6">Tahapan Aktif Saat Ini</th>
                            <th class="py-3.5 px-6">Lokasi Menu Penanganan</th>
                            <th class="py-3.5 px-6 text-center">Progres Total</th>
                            <th class="py-3.5 px-6 text-right">Tindakan Langsung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse ($projects as $project)
                            @php
                                $activeWf = $project->currentWorkflow?->workflow;
                                $meta = \App\Http\Controllers\Workflow\WorkflowTaskController::resolveTaskMeta($project);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6">
                                    <span class="text-xs font-bold text-slate-900 block">
                                        {{ $project->nama_project }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">
                                        {{ $project->aplikasi?->instansi?->nama_instansi ?? 'Instansi Pemohon' }}
                                        @if($project->pengajuan)
                                            • <span class="font-mono text-slate-500">{{ $project->pengajuan->kode_pengajuan }}</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($activeWf)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[17px]">{{ $activeWf->ikon ?? 'account_tree' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-xs font-bold text-slate-800 block">{{ $activeWf->nama }}</span>
                                                <span class="text-[10px] text-slate-400">
                                                    Penanggung Jawab: <strong class="text-slate-600">{{ $activeWf->role ? ucwords(str_replace('_', ' ', $activeWf->role->name)) : 'Terbuka' }}</strong>
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum diset</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                        <span class="material-symbols-outlined text-[15px] text-slate-500">{{ $meta['icon'] }}</span>
                                        <span>{{ $meta['menu'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="w-24 mx-auto">
                                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-600 mb-1">
                                            <span>{{ $project->progres_total }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#0D47A1] h-1.5 rounded-full transition-all duration-300" style="width: {{ $project->progres_total }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ $meta['url'] }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-[#0D47A1] hover:bg-[#1565C0] shadow-xs transition">
                                            <span>{{ $meta['label'] }}</span>
                                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                        </a>
                                        <a href="{{ route('workflow.task.show', [$project, 'no_redirect' => 1]) }}" title="Lihat Formulir Parameter Alur" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                            <span class="material-symbols-outlined text-[18px]">checklist</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <span class="material-symbols-outlined text-[28px]">done_all</span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Tidak Ada Antrean Tahapan Proyek</p>
                                    <p class="text-xs text-slate-400 mt-1">Semua tahapan proyek yang menjadi tanggung jawab Anda saat ini telah selesai diproses.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if ($projects->hasPages())
        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    @endif

</div>
@endsection