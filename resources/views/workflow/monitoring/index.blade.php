@extends(auth()->user()->userLayout())

@section('title', 'Monitoring Alur Workflow Aplikasi')
@section('page-title', 'Monitoring Alur Workflow')
@section('breadcrumb', 'Alur Kerja / Monitoring Alur')

@section('content')
<div class="space-y-6">

    <!-- Header & Info Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0D47A1] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">timeline</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pemantauan Alur Workflow Aplikasi Berjalan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pantau posisi siklus hidup (*lifecycle*), tahapan verifikasi, dan penanggung jawab aktif proyek aplikasi daerah se-Provinsi Riau secara *real-time*.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($isOpd)
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px]">domain</span>
                    <span>Mode OPD: {{ auth()->user()->instansi->nama_instansi ?? 'Instansi Anda' }}</span>
                </div>
            @else
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    <span>Monitoring Antar-Bidang Kominfo & OPD</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Ringkasan Statistik Tahapan Berjalan -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Total Berjalan -->
        <a href="{{ route('workflow.monitoring.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#0D47A1]/40 transition group {{ empty($statusTahapan) || $statusTahapan === 'all' ? 'ring-2 ring-[#0D47A1]/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Aktif</span>
                <span class="material-symbols-outlined text-slate-400 group-hover:text-[#0D47A1] text-[18px]">hub</span>
            </div>
            <div class="text-xl font-extrabold text-slate-900 mt-2">{{ $stats['total_berjalan'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Semua proyek berjalan</div>
        </a>

        <!-- Pengerjaan Tim -->
        <a href="{{ route('workflow.monitoring.index', array_merge(request()->query(), ['status_tahapan' => 'in_development'])) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-indigo-300 transition group {{ $statusTahapan === 'in_development' ? 'ring-2 ring-indigo-200' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">Pengerjaan</span>
                <span class="material-symbols-outlined text-indigo-500 text-[18px]">engineering</span>
            </div>
            <div class="text-xl font-extrabold text-indigo-900 mt-2">{{ $stats['in_development'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Coding & Desain Tim</div>
        </a>

        <!-- Uji Fungsional -->
        <a href="{{ route('workflow.monitoring.index', array_merge(request()->query(), ['status_tahapan' => 'functional_testing'])) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-amber-300 transition group {{ $statusTahapan === 'functional_testing' ? 'ring-2 ring-amber-200' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Uji Fungsional</span>
                <span class="material-symbols-outlined text-amber-500 text-[18px]">fact_check</span>
            </div>
            <div class="text-xl font-extrabold text-amber-900 mt-2">{{ $stats['functional_testing'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Tahap Uji APTIKA</div>
        </a>

        <!-- Uji Keamanan CSIRT -->
        <a href="{{ route('workflow.monitoring.index', array_merge(request()->query(), ['status_tahapan' => 'security_testing'])) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-rose-300 transition group {{ $statusTahapan === 'security_testing' ? 'ring-2 ring-rose-200' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Uji Keamanan</span>
                <span class="material-symbols-outlined text-rose-500 text-[18px]">security</span>
            </div>
            <div class="text-xl font-extrabold text-rose-900 mt-2">{{ $stats['security_testing'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Audit CSIRT/Persandian</div>
        </a>

        <!-- Deployment Server -->
        <a href="{{ route('workflow.monitoring.index', array_merge(request()->query(), ['status_tahapan' => 'deployment'])) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-cyan-300 transition group {{ $statusTahapan === 'deployment' ? 'ring-2 ring-cyan-200' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-cyan-700 uppercase tracking-wider">Deployment</span>
                <span class="material-symbols-outlined text-cyan-500 text-[18px]">dns</span>
            </div>
            <div class="text-xl font-extrabold text-cyan-900 mt-2">{{ $stats['deployment'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Alokasi Server TIK</div>
        </a>

        <!-- Live -->
        <a href="{{ route('workflow.monitoring.index', array_merge(request()->query(), ['status_tahapan' => 'live'])) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-emerald-300 transition group {{ $statusTahapan === 'live' ? 'ring-2 ring-emerald-200' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Selesai / Live</span>
                <span class="material-symbols-outlined text-emerald-500 text-[18px]">verified</span>
            </div>
            <div class="text-xl font-extrabold text-emerald-900 mt-2">{{ $stats['live'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Telah Beroperasi</div>
        </a>
    </div>

    <!-- Filter & Pencarian Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <form method="GET" action="{{ route('workflow.monitoring.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
            
            <!-- Input Cari Kata Kunci -->
            <div class="relative lg:col-span-2">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama aplikasi, nomor registrasi, instansi, atau tahapan..." 
                    class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
            </div>

            <!-- Filter Layanan -->
            <div>
                <select name="layanan_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                    <option value="">Semua Jenis Layanan</option>
                    @foreach($layanans as $layanan)
                        <option value="{{ $layanan->id }}" {{ request('layanan_id') == $layanan->id ? 'selected' : '' }}>
                            {{ $layanan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Instansi OPD (Khusus Non-OPD) -->
            @if(!$isOpd)
                <div>
                    <select name="instansi_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                        <option value="">Semua Perangkat Daerah</option>
                        @foreach($instansis as $inst)
                            <option value="{{ $inst->id }}" {{ request('instansi_id') == $inst->id ? 'selected' : '' }}>
                                {{ Str::limit($inst->nama_instansi, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <select name="status_tahapan" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]">
                        <option value="all">Semua Status Tahapan</option>
                        <option value="in_development" {{ request('status_tahapan') === 'in_development' ? 'selected' : '' }}>Pengerjaan Tim</option>
                        <option value="functional_testing" {{ request('status_tahapan') === 'functional_testing' ? 'selected' : '' }}>Uji Fungsional</option>
                        <option value="security_testing" {{ request('status_tahapan') === 'security_testing' ? 'selected' : '' }}>Uji Keamanan Siber</option>
                        <option value="deployment" {{ request('status_tahapan') === 'deployment' ? 'selected' : '' }}>Deployment Server</option>
                        <option value="live" {{ request('status_tahapan') === 'live' ? 'selected' : '' }}>Live</option>
                    </select>
                </div>
            @endif

            <!-- Tombol Aksi -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-[#0D47A1] hover:bg-[#1565C0] text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Terapkan</span>
                </button>
                @if(request()->anyFilled(['search', 'layanan_id', 'instansi_id', 'status_tahapan']))
                    <a href="{{ route('workflow.monitoring.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Reset Filter">
                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Daftar Proyek & Visual Pipeline Stepper -->
    <div class="space-y-4">
        @forelse ($projects as $project)
            @php
                $layanan = $project->pengajuan?->layanan;
                $workflows = $layanan?->workflows ?? collect();
                $workflowDataCollection = $project->pengajuan?->workflowData ?? collect();
                $currentWf = $project->currentWorkflow?->workflow;
                $activeRole = $currentWf?->role?->name ?? 'Tim Kominfo';
                $roleLabel = match($activeRole) {
                    'opd' => 'Perangkat Daerah (OPD)',
                    'admin_aptika' => 'Bidang APTIKA',
                    'ketua_tim', 'anggota_tim' => 'Tim Pengembang',
                    'admin_persandian' => 'Bidang Persandian (CSIRT)',
                    'admin_infrastruktur' => 'Bidang Infrastruktur (TIK)',
                    default => 'Penanggung Jawab Sistem',
                };
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#0D47A1]/40 transition overflow-hidden">
                <!-- Header Kartu Proyek -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono text-xs font-bold text-[#0D47A1] bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-100">
                                {{ $project->pengajuan->kode_pengajuan ?? 'PROJ-' . $project->id }}
                            </span>
                            @if($layanan)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border" style="background-color: {{ $layanan->warna }}15; color: {{ $layanan->warna }}; border-color: {{ $layanan->warna }}30;">
                                    {{ $layanan->nama }}
                                </span>
                            @endif
                            <span class="text-xs text-slate-500 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">domain</span>
                                <span>{{ $project->aplikasi->instansi->nama_instansi ?? ($project->pengajuan->instansi->nama_instansi ?? 'Pemerintah Provinsi Riau') }}</span>
                            </span>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">
                            {{ $project->nama_project }}
                        </h3>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-right">
                            <div class="text-[11px] text-slate-400 font-semibold">Progres Keseluruhan</div>
                            <div class="text-sm font-extrabold text-slate-900">{{ (int) $project->progres_total }}%</div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-center p-1">
                            <div class="relative w-full h-full flex items-center justify-center">
                                <div class="text-xs font-bold text-[#0D47A1]">{{ (int) $project->progres_total }}%</div>
                            </div>
                        </div>
                        @can('workflow.monitoring.detail')
                            <a href="{{ route('workflow.monitoring.show', $project) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0D47A1] hover:bg-[#1565C0] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                                <span>Detail Alur</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Visual Stepper Pipeline Bar -->
                <div class="p-5 sm:p-6 bg-slate-50/50 space-y-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Peta Posisi Alur Workflow</span>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#0D47A1]">
                                <span class="w-2 h-2 rounded-full bg-[#0D47A1] animate-ping"></span> Aktif
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span> Menunggu
                            </span>
                        </div>
                    </div>

                    <!-- Pipeline Grid -->
                    @if($workflows->isNotEmpty())
                        @php
                            $currentUrutan = $project->currentWorkflow?->urutan ?? 0;
                            $isLive = $project->status === 'live';
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                            @foreach ($workflows as $lw)
                                @php
                                    $wfData = $workflowDataCollection->firstWhere('layanan_workflow_id', $lw->id);
                                    $isCurrent = $project->current_workflow_id === $lw->id && ! $isLive;
                                    $isCompleted = $isLive || ($currentUrutan > 0 && $lw->urutan < $currentUrutan) || ($wfData && $wfData->status === 'completed');
                                    if ($isCurrent) {
                                        $isCompleted = false;
                                    }
                                    $status = $isCompleted ? 'completed' : ($isCurrent ? 'in_progress' : 'pending');
                                @endphp
                                <div class="p-3 rounded-xl border transition flex flex-col justify-between {{ $status === 'completed' ? 'bg-emerald-50/60 border-emerald-200 text-emerald-950' : ($status === 'in_progress' ? 'bg-blue-50/80 border-[#0D47A1]/40 ring-2 ring-blue-100 text-[#0D47A1]' : 'bg-white border-slate-200/80 text-slate-500') }}">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-extrabold {{ $status === 'completed' ? 'bg-emerald-600 text-white' : ($status === 'in_progress' ? 'bg-[#0D47A1] text-white animate-pulse' : 'bg-slate-100 text-slate-500') }}">
                                            @if($status === 'completed')
                                                <span class="material-symbols-outlined text-[14px]">check</span>
                                            @elseif($status === 'in_progress')
                                                <span class="material-symbols-outlined text-[14px]">sync</span>
                                            @else
                                                <span>{{ $lw->urutan }}</span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] font-bold uppercase tracking-wider {{ $status === 'completed' ? 'text-emerald-700' : ($status === 'in_progress' ? 'text-[#0D47A1]' : 'text-slate-400') }}">
                                            {{ $status === 'completed' ? 'Selesai' : ($status === 'in_progress' ? 'Aktif' : 'Antrean') }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold leading-snug line-clamp-1" title="{{ $lw->workflow->nama }}">
                                            {{ $lw->workflow->nama }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">
                                            {{ $lw->workflow->role?->name ?? 'Kominfo' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Fallback jika belum memiliki pipeline layanan dinamis -->
                        <div class="p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-500">
                            Status Proyek: <strong class="text-slate-800">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</strong>
                        </div>
                    @endif

                    <!-- Status Bar Bawah: Current Active Step Info -->
                    <div class="pt-3 border-t border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#0D47A1] text-[18px]">play_circle</span>
                            <span class="text-slate-600">
                                Posisi Tahapan Aktif: 
                                <strong class="text-slate-900">{{ $currentWf->nama ?? ucfirst(str_replace('_', ' ', $project->status)) }}</strong>
                            </span>
                            <span class="text-slate-400">•</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                <span class="material-symbols-outlined text-[13px]">person</span>
                                <span>{{ $roleLabel }}</span>
                            </span>
                        </div>
                        <div class="text-slate-400 text-[11px]">
                            Pembaruan Terakhir: <strong>{{ $project->updated_at->diffForHumans() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[36px]">timeline</span>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tidak Ada Proyek Berjalan yang Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tidak ditemukan data proyek aplikasi yang sesuai dengan filter atau kata kunci pencarian Anda saat ini.
                </p>
                <div class="mt-4">
                    <a href="{{ route('workflow.monitoring.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        Reset Semua Filter
                    </a>
                </div>
            </div>
        @endforelse

        <!-- Pagination -->
        @if ($projects->hasPages())
            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
