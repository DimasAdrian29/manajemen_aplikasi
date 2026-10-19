@extends('layouts.aptika')

@section('title', 'Tambah Master Workflow')
@section('page-title', 'Tambah Master Alur Kerja')
@section('breadcrumb', 'Konfigurasi Alur Layanan / Master Workflow / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Formulir Master Workflow Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Definisikan tahapan alur kerja yang dapat digunakan pada ragam layanan.</p>
            </div>
            <a href="{{ route('aptika.workflow.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </a>
        </div>

        <form method="POST" action="{{ route('aptika.workflow.store') }}" class="p-6 space-y-5">
            @csrf

            <!-- Nama Workflow -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Tahapan Workflow <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    value="{{ old('nama') }}" 
                    required 
                    placeholder="Contoh: Pengajuan Berkas, Uji Fungsional, Telaah Arsitektur..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                @error('nama')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi Tahapan
                </label>
                <textarea 
                    name="deskripsi" 
                    rows="3" 
                    placeholder="Penjelasan apa saja yang dilakukan pada tahapan alur ini..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Peran Penanggung Jawab / Pelaksana Tahapan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Peran Pelaksana / Penanggung Jawab Tahapan
                </label>
                <select 
                    name="role_id" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] bg-white"
                >
                    <option value="">-- Tanpa Peran Tertentu / Umum --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }} ({{ ucwords(str_replace('_', ' ', $r->name)) }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">
                    Pengguna dengan peran ini yang akan menerima tugas pada antrean kerja dan berhak memvalidasi/memajukan tahapan alur.
                </p>
                @error('role_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ikon Material Symbols -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Ikon (Material Symbols)
                </label>
                <input 
                    type="text" 
                    id="wfIconInput"
                    name="ikon" 
                    value="{{ old('ikon', 'account_tree') }}" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                >
                <div class="flex items-center gap-1.5 mt-2 flex-wrap text-xs text-slate-400">
                    <span>Pilihan cepat:</span>
                    <button type="button" onclick="document.getElementById('wfIconInput').value='upload_file'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">upload_file</button>
                    <button type="button" onclick="document.getElementById('wfIconInput').value='fact_check'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">fact_check</button>
                    <button type="button" onclick="document.getElementById('wfIconInput').value='engineering'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">engineering</button>
                    <button type="button" onclick="document.getElementById('wfIconInput').value='checklist'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">checklist</button>
                    <button type="button" onclick="document.getElementById('wfIconInput').value='security'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">security</button>
                    <button type="button" onclick="document.getElementById('wfIconInput').value='rocket_launch'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-600">rocket_launch</button>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('aptika.workflow.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition">
                    Simpan & Konfigurasi Field
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
