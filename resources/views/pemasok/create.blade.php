@extends('layouts.app', ['title' => 'Tambah Pemasok', 'breadcrumb' => 'Pemasok > Tambah'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Pemasok Baru</h2>
            <p class="text-xs text-slate-500">Daftarkan mitra distributor atau toko grosir pemasok warung</p>
        </div>
        <a href="{{ route('pemasok.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('pemasok.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="nama_pemasok" class="block text-xs font-bold text-slate-700 mb-1">Nama Pemasok <span class="text-rose-500">*</span></label>
                <input type="text" id="nama_pemasok" name="nama_pemasok" required value="{{ old('nama_pemasok') }}" placeholder="Contoh: Toko Grosir Sumber Makmur"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600">
                @error('nama_pemasok')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nomor_telepon" class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="08xxxxxxxxxx"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600">
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="pemasok@mail.com"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600">
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-xs font-bold text-slate-700 mb-1">Alamat Gudang / Toko</label>
                <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600">{{ old('alamat') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pemasok.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md">Simpan Pemasok</button>
            </div>
        </form>
    </div>
</div>
@endsection
