@extends('layouts.eksekutif')

@section('title', 'Ubah Aplikasi')
@section('page-title', 'Ubah Data Aplikasi')
@section('breadcrumb', 'Monitoring Aplikasi / Ubah')

@section('content')
<form method="POST" action="{{ route('eksekutif.monitoring-aplikasi.update', $aplikasi) }}" class="max-w-5xl mx-auto space-y-6">
    @csrf
    @method('PUT')

    @include('eksekutif.monitoring-aplikasi._form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('eksekutif.monitoring-aplikasi.show', $aplikasi) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
            Batal
        </a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] text-white text-xs font-bold hover:bg-[#1565C0] shadow-sm transition">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection
