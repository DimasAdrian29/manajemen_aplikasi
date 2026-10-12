@extends('layouts.admin')

@section('title', 'Master User: Tambah Pengguna')
@section('page-title', 'Tambah Pengguna Baru')
@section('breadcrumb', 'Master User / User / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Formulir Akun Pengguna</h2>
                <p class="text-xs text-slate-500 mt-1">Daftarkan pengguna internal dinas atau akun perwakilan OPD.</p>
            </div>
            <a href="{{ route('superadmin.user.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        <form method="POST" action="{{ route('superadmin.user.store') }}" class="space-y-5">
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
                    placeholder="Contoh: Budi Santoso, S.Kom" 
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
                        placeholder="budisantoso" 
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
                        placeholder="budi@riau.go.id" 
                        class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1]" 
                        required
                    >
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kata Sandi <span class="text-rose-500">*</span>
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

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Peran Sistem <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="role" 
                    id="roleSelect" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('role') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer" 
                    required
                >
                    <option value="">-- Pilih Peran Pengguna --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ old('role') === $r->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Instansi Selection (Conditional for OPD) -->
            <div id="instansiGroup" class="transition-all duration-200 {{ old('role') === 'opd' ? '' : 'hidden' }}">
                <label for="instansi_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Instansi OPD Terkait <span class="text-rose-500">*</span>
                </label>
                <select 
                    name="instansi_id" 
                    id="instansi_id" 
                    class="block w-full px-3.5 py-2.5 bg-slate-50/50 border @error('instansi_id') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/20 focus:border-[#0D47A1] cursor-pointer"
                >
                    <option value="">-- Pilih Instansi OPD --</option>
                    @foreach ($instansis as $ins)
                        <option value="{{ $ins->id }}" {{ old('instansi_id') == $ins->id ? 'selected' : '' }}>
                            {{ $ins->nama_instansi }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">*Wajib dipilih apabila peran pengguna adalah OPD.</p>
                @error('instansi_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Group -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.user.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0D47A1] hover:bg-[#165eae] text-white text-sm font-semibold shadow-sm shadow-[#0D47A1]/20 transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    <span>Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const roleSelect = document.getElementById('roleSelect');
    const instansiGroup = document.getElementById('instansiGroup');
    const instansiSelect = document.getElementById('instansi_id');

    if (roleSelect && instansiGroup) {
        roleSelect.addEventListener('change', function() {
            if (this.value === 'opd') {
                instansiGroup.classList.remove('hidden');
                instansiSelect.setAttribute('required', 'required');
            } else {
                instansiGroup.classList.add('hidden');
                instansiSelect.removeAttribute('required');
                instansiSelect.value = '';
            }
        });
    }
</script>
@endsection
