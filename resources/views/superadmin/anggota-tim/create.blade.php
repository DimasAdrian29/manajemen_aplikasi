@extends('layouts.admin')

@section('title', 'Tambah Anggota Tim Pengembang')
@section('page-title', 'Tambah Personil Tim Pengembang')
@section('breadcrumb', 'Master Data / Anggota Tim / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Formulir Personil Pengembang</h2>
                <p class="text-xs text-slate-500 mt-1">Daftarkan tenaga teknis internal yang siap ditugaskan pada proyek aplikasi.</p>
            </div>
            <a href="{{ route('superadmin.anggota-tim.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('superadmin.anggota-tim.store') }}" class="space-y-5">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}" 
                    placeholder="Contoh: Rian Pratama, S.Kom" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                    required
                >
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Username <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        value="{{ old('username') }}" 
                        placeholder="rianpratama" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('username') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                        required
                    >
                    @error('username')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        placeholder="rian@riau.go.id" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                        required
                    >
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- No HP / WhatsApp -->
                <div>
                    <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor HP / WhatsApp
                    </label>
                    <input 
                        type="text" 
                        name="no_hp" 
                        id="no_hp" 
                        value="{{ old('no_hp') }}" 
                        placeholder="0812xxxxxxxx" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('no_hp') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    @error('no_hp')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keahlian / Posisi Teknis -->
                <div>
                    <label for="jabatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Keahlian / Posisi Teknis
                    </label>
                    <input 
                        type="text" 
                        name="jabatan" 
                        id="jabatan" 
                        value="{{ old('jabatan') }}" 
                        list="posisiList" 
                        placeholder="Contoh: Backend Developer (PHP/Laravel)" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('jabatan') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]"
                    >
                    <datalist id="posisiList">
                        <option value="Lead Developer / Project Manager">
                        <option value="Programmer Fullstack">
                        <option value="Backend Developer (PHP/Laravel)">
                        <option value="Frontend Developer (Tailwind/Vue/React)">
                        <option value="Mobile Developer (Flutter/Android)">
                        <option value="System Analyst">
                        <option value="UI/UX Designer">
                        <option value="Database Administrator">
                        <option value="QA & Software Tester">
                        <option value="DevOps & Server Engineer">
                        <option value="Technical Writer / Dokumentasi">
                    </datalist>
                    @error('jabatan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Note -->
            <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80 text-xs text-blue-900 flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[18px] text-[#0D47A1] shrink-0 mt-0.5">info</span>
                <p>
                    <strong>Fleksibilitas Penugasan Proyek:</strong> Personil yang didaftarkan dapat ditunjuk secara fleksibel sebagai <strong>Ketua Tim (Lead)</strong> atau <strong>Anggota Tim</strong> pada setiap proyek aplikasi saat inisiasi oleh Bidang APTIKA.
                </p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kata Sandi Akun <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="Minimal 6 karakter" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                    required
                >
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.anggota-tim.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    <span>Simpan Personil</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
