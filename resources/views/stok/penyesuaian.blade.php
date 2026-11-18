@extends('layouts.app', ['title' => 'Penyesuaian Stok', 'breadcrumb' => 'Stok > Penyesuaian'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Formulir Penyesuaian Stok</h2>
            <p class="text-xs text-slate-500">Catat penambahan atau pengurangan stok karena barang rusak, hilang, retur, atau selisih fisik</p>
        </div>
        <a href="{{ route('stok.riwayat') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Riwayat</span>
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('stok.penyesuaian.submit') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Pilih Barang -->
            <div>
                <label for="barang_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Pilih Barang <span class="text-rose-500">*</span>
                </label>
                <select id="barang_id" name="barang_id" required onchange="updateCurrentStock(this)"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangList as $b)
                        <option value="{{ $b->id }}" data-stok="{{ $b->stok_saat_ini }}" data-satuan="{{ $b->satuan?->singkatan }}" {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_barang }} (Kode: {{ $b->kode_barang }})
                        </option>
                    @endforeach
                </select>
                @error('barang_id')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Stock Info -->
            <div id="boxStokSaatIni" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
                <span class="text-slate-500 font-semibold">Stok Tercatat Sistem Saat Ini:</span>
                <span id="stokDisplay" class="font-extrabold text-sm text-slate-900">-</span>
            </div>

            <!-- Jenis Penyesuaian -->
            <div>
                <label for="jenis_pergerakan" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Jenis Penyesuaian <span class="text-rose-500">*</span>
                </label>
                <select id="jenis_pergerakan" name="jenis_pergerakan" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="penyesuaian_masuk" {{ old('jenis_pergerakan') === 'penyesuaian_masuk' ? 'selected' : '' }}>
                        (+) Penyesuaian Masuk (Selisih lebih / barang ditemukan)
                    </option>
                    <option value="penyesuaian_keluar" {{ old('jenis_pergerakan') === 'penyesuaian_keluar' ? 'selected' : '' }}>
                        (-) Penyesuaian Keluar (Barang rusak / hilang / tercecer / selisih kurang)
                    </option>
                    <option value="retur_masuk" {{ old('jenis_pergerakan') === 'retur_masuk' ? 'selected' : '' }}>
                        (+) Retur Masuk (Barang dikembalikan oleh pembeli)
                    </option>
                    <option value="retur_keluar" {{ old('jenis_pergerakan') === 'retur_keluar' ? 'selected' : '' }}>
                        (-) Retur Keluar (Pengembalian barang ke distributor / pemasok)
                    </option>
                </select>
                @error('jenis_pergerakan')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jumlah -->
            <div>
                <label for="jumlah" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Jumlah Kuantitas Penyesuaian <span class="text-rose-500">*</span>
                </label>
                <input type="number" step="0.001" id="jumlah" name="jumlah" required min="0.001"
                    value="{{ old('jumlah') }}" placeholder="Contoh: 5"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-brand-600">
                <p class="mt-1 text-[11px] text-slate-400">*Sistem tidak memperbolehkan stok menjadi negatif.</p>
                @error('jumlah')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan / Alasan -->
            <div>
                <label for="catatan" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan / Keterangan Penyesuaian <span class="text-rose-500">*</span>
                </label>
                <textarea id="catatan" name="catatan" rows="3" required
                    placeholder="Contoh: Kemasan sobek / expired saat sortir rak gudang"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('stok.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Eksekusi Penyesuaian Stok</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateCurrentStock(select) {
        const option = select.options[select.selectedIndex];
        const stok = option.getAttribute('data-stok');
        const satuan = option.getAttribute('data-satuan');
        const display = document.getElementById('stokDisplay');
        if (stok) {
            display.textContent = Number(stok).toLocaleString('id-ID') + ' ' + (satuan || '');
        } else {
            display.textContent = '-';
        }
    }
    // run on initial load
    document.addEventListener('DOMContentLoaded', function() {
        const sel = document.getElementById('barang_id');
        if (sel && sel.value) {
            updateCurrentStock(sel);
        }
    });
</script>
@endsection
