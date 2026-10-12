<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Portal {{ $roleName }} - SIMBANGDA Riau</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body {
            overflow-x: hidden;
            overscroll-behavior-x: none;
        }
    </style>
</head>
<body class="bg-[#f4f6fa] text-slate-800 font-['Plus_Jakarta_Sans',sans-serif] min-h-screen flex flex-col items-center justify-center p-6 overflow-x-hidden w-full">
    <div class="max-w-md w-full bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 text-center">
        <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200/80 shadow-xs p-2 flex items-center justify-center mx-auto mb-4 shrink-0">
            <img src="{{ asset('images/kominfo-seeklogo.png') }}" alt="Logo Kominfo" class="w-full h-full object-contain">
        </div>
        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#0D47A1] uppercase mb-2">
            Peran: {{ $roleName }}
        </span>
        <h1 class="text-xl font-bold text-slate-900 mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-xs text-slate-500 leading-relaxed mb-6">
            Portal khusus untuk <strong>{{ $roleName }}</strong> sedang disiapkan sesuai tahapan pengembangan sistem SIMBANGDA Diskominfo Riau.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 text-xs font-semibold transition cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                <span>Keluar Akun</span>
            </button>
        </form>
    </div>
</body>
</html>
