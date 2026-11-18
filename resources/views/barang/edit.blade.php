@extends('layouts.app', ['title' => 'Ubah Barang', 'breadcrumb' => 'Data Barang > Ubah'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Ubah Data Barang</h2>
            <p class="text-xs text-slate-500">{{ $barang->nama_barang }} (Kode: {{ $barang->kode_barang }})</p>
        </div>
        <a href="{{ route('barang.show', $barang->id) }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('barang.update', $barang->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Kode Barang -->
                <div>
                    <label for="kode_barang" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kode Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="kode_barang" name="kode_barang" required
                        value="{{ old('kode_barang', $barang->kode_barang) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-brand-600">
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
                        value="{{ old('nama_barang', $barang->nama_barang) }}"
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
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id', $barang->kategori_id) == $kat->id ? 'selected' : '' }}>
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
                        @foreach($satuans as $sat)
                            <option value="{{ $sat->id }}" {{ old('satuan_id', $barang->satuan_id) == $sat->id ? 'selected' : '' }}>
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
                        value="{{ old('harga_beli', $barang->harga_beli) }}"
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
                        value="{{ old('harga_jual', $barang->harga_jual) }}"
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
                        value="{{ old('stok_minimum', $barang->stok_minimum) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    @error('stok_minimum')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Stok Saat Ini -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Stok Saat Ini (Terkunci)</label>
                    <div class="px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold text-slate-800 flex justify-between items-center">
                        <span>{{ number_format($barang->stok_saat_ini, 0, ',', '.') }} {{ $barang->satuan?->singkatan }}</span>
                        <a href="{{ route('stok.penyesuaian') }}" class="text-[11px] text-brand-700 font-bold hover:underline">
                            Sesuaikan Stok &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi / Keterangan Barang</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
            </div>

            <!-- Status Aktif Checkbox -->
            <div class="flex items-center">
                <input id="status_aktif" name="status_aktif" type="checkbox" value="1" {{ old('status_aktif', $barang->status_aktif) ? 'checked' : '' }}
                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded">
                <label for="status_aktif" class="ml-2 block text-xs font-bold text-slate-700">Barang Aktif</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('barang.show', $barang->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
