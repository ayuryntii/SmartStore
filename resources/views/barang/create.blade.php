@extends('layouts.app', ['title' => 'Tambah Barang', 'breadcrumb' => 'Data Barang > Tambah'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Master Barang Baru</h2>
            <p class="text-xs text-slate-500">Daftarkan item produk warung beserta pengaturan stok awal dan harga</p>
        </div>
        <a href="{{ route('barang.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('barang.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Kode Barang -->
                <div>
                    <label for="kode_barang" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kode Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="kode_barang" name="kode_barang" required
                        value="{{ old('kode_barang', $suggestedCode) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <p class="mt-1 text-[11px] text-slate-400">Kode unik internal warung (dapat diedit manual)</p>
                    @error('kode_barang')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Barang -->
                <div>
                    <label for="nama_barang" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama_barang" name="nama_barang" required
                        value="{{ old('nama_barang') }}" placeholder="Contoh: Indomie Goreng Spesial 85g"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    @error('nama_barang')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kategori Barang <span class="text-rose-500">*</span>
                    </label>
                    <select id="kategori_id" name="kategori_id" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Satuan -->
                <div>
                    <label for="satuan_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Satuan Satuan <span class="text-rose-500">*</span>
                    </label>
                    <select id="satuan_id" name="satuan_id" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                        <option value="">Pilih Satuan</option>
                        @foreach($satuans as $sat)
                            <option value="{{ $sat->id }}" {{ old('satuan_id') == $sat->id ? 'selected' : '' }}>
                                {{ $sat->nama_satuan }} ({{ $sat->singkatan }})
                            </option>
                        @endforeach
                    </select>
                    @error('satuan_id')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Beli -->
                <div>
                    <label for="harga_beli" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Harga Beli / Kulakan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" id="harga_beli" name="harga_beli" required min="0"
                        value="{{ old('harga_beli', 0) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-brand-600">
                    @error('harga_beli')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Jual -->
                <div>
                    <label for="harga_jual" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Harga Jual Konsumen (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" id="harga_jual" name="harga_jual" required min="0"
                        value="{{ old('harga_jual', 0) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-brand-600">
                    @error('harga_jual')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stok Minimum -->
                <div>
                    <label for="stok_minimum" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Batas Stok Minimum <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.001" id="stok_minimum" name="stok_minimum" required min="0"
                        value="{{ old('stok_minimum', 5) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <p class="mt-1 text-[11px] text-slate-400">Peringatan otomatis muncul jika stok &le; angka ini</p>
                    @error('stok_minimum')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stok Awal -->
                <div>
                    <label for="stok_awal" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Stok Fisik Awal
                    </label>
                    <input type="number" step="0.001" id="stok_awal" name="stok_awal" min="0"
                        value="{{ old('stok_awal', 0) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <p class="mt-1 text-[11px] text-slate-400">Dicatat otomatis sebagai riwayat penyesuaian stok masuk</p>
                    @error('stok_awal')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi / Keterangan Barang</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    placeholder="Keterangan tambahan barang, lokasi rak penyimpanan, varian..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Status Aktif Checkbox -->
            <div class="flex items-center">
                <input id="status_aktif" name="status_aktif" type="checkbox" value="1" checked
                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded">
                <label for="status_aktif" class="ml-2 block text-xs font-bold text-slate-700">Barang Aktif (Dapat dijual di kasir)</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('barang.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Barang</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
