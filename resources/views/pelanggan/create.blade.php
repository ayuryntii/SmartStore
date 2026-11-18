@extends('layouts.app', ['title' => 'Tambah Pelanggan', 'breadcrumb' => 'Pelanggan > Tambah'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Pelanggan Baru</h2>
            <p class="text-xs text-slate-500">Daftarkan data pembeli/konsumen warung</p>
        </div>
        <a href="{{ route('pelanggan.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('pelanggan.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="nama_pelanggan" class="block text-xs font-bold text-slate-700 mb-1">Nama Pelanggan <span class="text-rose-500">*</span></label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" required value="{{ old('nama_pelanggan') }}" placeholder="Contoh: Ibu Rina / Pak Joko"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600">
                @error('nama_pelanggan')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nomor_telepon" class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="08xxxxxxxxxx"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600">
            </div>

            <div>
                <label for="alamat" class="block text-xs font-bold text-slate-700 mb-1">Alamat Domisili</label>
                <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat atau lokasi rumah pelanggan..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600">{{ old('alamat') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pelanggan.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md">Simpan Pelanggan</button>
            </div>
        </form>
    </div>
</div>
@endsection
