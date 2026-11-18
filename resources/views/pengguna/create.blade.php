@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page-title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6">
        <div class="flex items-center justify-between pb-5 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Form Pengguna Baru</h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftarkan operator sistem baru dengan hak akses yang ditentukan.</p>
            </div>
            <a href="{{ route('pengguna.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <form action="{{ route('pengguna.store') }}" method="POST" class="mt-6 space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso"
                       class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 @error('nama') border-rose-500 @enderror">
                @error('nama')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="Contoh: budi@bawara.id"
                       class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 @error('email') border-rose-500 @enderror">
                @error('email')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi (Password) <span class="text-rose-500">*</span></label>
                <input type="password" name="kata_sandi" required placeholder="Minimal 6 karakter"
                       class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 @error('kata_sandi') border-rose-500 @enderror">
                @error('kata_sandi')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Peran Akses (Role) <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:border-emerald-500 transition-colors has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                        <input type="radio" name="peran" value="admin" class="sr-only" {{ old('peran') === 'admin' ? 'checked' : '' }}>
                        <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                            <i data-lucide="shield" class="w-4 h-4 text-purple-600"></i> Admin
                        </span>
                        <span class="text-[11px] text-gray-500 mt-1">Akses penuh seluruh modul, persediaan, pembelian, analisis, laporan, dan pengaturan.</span>
                    </label>

                    <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:border-emerald-500 transition-colors has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                        <input type="radio" name="peran" value="kasir" class="sr-only" {{ old('peran', 'kasir') === 'kasir' ? 'checked' : '' }}>
                        <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                            <i data-lucide="shopping-cart" class="w-4 h-4 text-blue-600"></i> Kasir
                        </span>
                        <span class="text-[11px] text-gray-500 mt-1">POS Kasir transaksi penjualan, piutang, dan pencarian informasi barang.</span>
                    </label>
                </div>
                @error('peran')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="status_aktif" value="1" checked
                           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-medium text-gray-700">Akun Langsung Aktif</span>
                </label>
            </div>

            <div class="pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('pengguna.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors shadow-xs">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
