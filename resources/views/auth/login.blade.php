@extends('layouts.guest', ['title' => 'Masuk ke Sistem'])

@section('content')
<form class="space-y-5" action="{{ route('login.post') }}" method="POST">
    @csrf

    <div>
        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="mail" class="w-5 h-5"></i>
            </div>
            <input id="email" name="email" type="email" autocomplete="email" required
                value="{{ old('email') }}"
                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-brand-600 sm:text-sm transition duration-150"
                placeholder="nama@bawara.id">
        </div>
        @error('email')
            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kata_sandi" class="block text-sm font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="lock" class="w-5 h-5"></i>
            </div>
            <input id="kata_sandi" name="kata_sandi" type="password" required
                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-brand-600 sm:text-sm transition duration-150"
                placeholder="••••••••">
        </div>
        @error('kata_sandi')
            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox"
                class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-slate-600">Ingat saya</label>
        </div>
    </div>

    <div>
        <button type="submit"
            class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl shadow-[0_4px_14px_rgba(118,148,76,0.28)] text-sm font-bold text-white bg-[#76944C] hover:bg-[#65803F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#76944C] transition duration-150">
            <i data-lucide="log-in" class="w-4 h-4"></i>
            <span>Masuk ke Akun</span>
        </button>
    </div>
</form>

<div class="mt-8 pt-6 border-t border-[#C0B6AC]/40">
    <div class="flex items-center justify-between mb-3">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
            Akses Cepat Pengujian (Role Matriks)
        </p>
        <span class="text-[10px] text-slate-500 bg-[#F6EFCF] px-2 py-0.5 rounded-full font-medium">Klik untuk isi otomatis</span>
    </div>
    <div class="grid grid-cols-2 gap-2.5">
        <button type="button" onclick="setDemo('admin@bawara.id', 'password123')"
            class="p-2.5 text-left rounded-xl bg-[#EEF4E5] hover:bg-[#E3ECD5] border border-[#C8DAA6] transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#31461D] flex items-center gap-1">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-[#5D7A37]"></i> Admin
                </span>
                <span class="text-[10px] text-[#5D7A37] font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
            </div>
            <p class="text-[10px] text-[#4A642E] mt-1 line-clamp-1">Akses penuh sistem, persediaan & laporan</p>
        </button>

        <button type="button" onclick="setDemo('kasir@bawara.id', 'password123')"
            class="p-2.5 text-left rounded-xl bg-[#FFF9DC] hover:bg-[#FFF2BE] border border-[#FFD21F]/70 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#694E00] flex items-center gap-1">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5 text-[#C29400]"></i> Kasir
                </span>
                <span class="text-[10px] text-[#C29400] font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
            </div>
            <p class="text-[10px] text-[#7A5B00] mt-1 line-clamp-1">POS kasir, piutang & info barang</p>
        </button>
    </div>

    <div class="mt-5 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-brand-700 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Kembali ke Halaman Utama</span>
        </a>
    </div>
</div>

<script>
    function setDemo(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('kata_sandi').value = pass;
    }
</script>
@endsection
