@can('inventaris.view')
    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
        Monitoring
    </div>

    <a href="{{ route('eksekutif.monitoring-aplikasi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('eksekutif.monitoring-aplikasi.*') ? 'bg-[#0D47A1] text-white shadow-sm shadow-[#0D47A1]/25' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0D47A1]' }}">
        <span class="material-symbols-outlined text-[20px]">monitoring</span>
        <span>Monitoring Aplikasi</span>
    </a>
@endcan
