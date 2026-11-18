@extends('layouts.app', ['title' => 'Prediksi Kebutuhan Stok', 'breadcrumb' => 'Analisis > Prediksi Kebutuhan'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Prediksi Kebutuhan Stok (Moving Average)</h2>
            <p class="text-xs text-slate-500">Estimasi jumlah kulakan barang optimal berdasarkan laju penjualan harian aktual</p>
        </div>
        <a href="{{ route('rencana-belanja.create') }}" class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i>
            <span>Buat Rencana Belanja Berdasarkan Hasil</span>
        </a>
    </div>

    <!-- Formula Information Box -->
    <div class="bg-gradient-to-r from-emerald-900 to-navy-900 p-5 rounded-3xl text-white text-xs space-y-2 shadow-lg">
        <div class="flex items-center gap-2 font-bold text-emerald-300">
            <i data-lucide="calculator" class="w-4 h-4"></i>
            <span>Rumus Perhitungan Moving Average</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1 text-slate-200">
            <div class="bg-white/10 p-3 rounded-2xl">
                <span class="font-bold text-white block mb-0.5">1. Kebutuhan Periode:</span>
                <code>Kebutuhan = Rata-rata Penjualan Harian &times; Hari Perencanaan</code>
            </div>
            <div class="bg-white/10 p-3 rounded-2xl">
                <span class="font-bold text-white block mb-0.5">2. Rekomendasi Pembelian:</span>
                <code>Rekomendasi = Maksimum (0, Kebutuhan + Stok Aman - Stok Saat Ini)</code>
            </div>
        </div>
    </div>

    <!-- Parameters Filter -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('analisis.prediksi') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Periode Riwayat Penjualan</label>
                <select name="history_days" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
                    <option value="7" {{ $historyDays == 7 ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="14" {{ $historyDays == 14 ? 'selected' : '' }}>14 Hari Terakhir</option>
                    <option value="30" {{ $historyDays == 30 ? 'selected' : '' }}>30 Hari Terakhir (Standar)</option>
                    <option value="60" {{ $historyDays == 60 ? 'selected' : '' }}>60 Hari Terakhir</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Hari Perencanaan Kedepan</label>
                <select name="planning_days" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
                    <option value="3" {{ $planningDays == 3 ? 'selected' : '' }}>3 Hari Kedepan</option>
                    <option value="7" {{ $planningDays == 7 ? 'selected' : '' }}>7 Hari / 1 Minggu Kedepan</option>
                    <option value="14" {{ $planningDays == 14 ? 'selected' : '' }}>14 Hari / 2 Minggu Kedepan</option>
                    <option value="30" {{ $planningDays == 30 ? 'selected' : '' }}>30 Hari / 1 Bulan Kedepan</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Kategori</label>
                <select name="kategori_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ $kategoriId == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Hitung Prediksi
                </button>
                <a href="{{ route('analisis.prediksi') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary of Prediction -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Total Produk Perlu Kulakan</span>
            <span class="text-2xl font-black text-brand-700">{{ $totalItemPerluBeli }} Produk</span>
            <span class="text-[11px] text-slate-400 block mt-1">Berdasarkan hasil kalkulasi moving average</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Estimasi Total Modal Diperlukan</span>
            <span class="text-2xl font-black text-slate-900">Rp {{ number_format($totalEstimasiBiaya, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">Dihitung dari harga kulakan terkini</span>
        </div>
    </div>

    <!-- Table of Predictions -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Hasil Rekomendasi Pengadaan per Barang
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4 text-center">Rata-rata/Hari</th>
                        <th class="py-3 px-4 text-center">Kebutuhan ({{ $planningDays }} Hari)</th>
                        <th class="py-3 px-4 text-center">Stok Saat Ini</th>
                        <th class="py-3 px-4 text-center">Stok Aman (Min)</th>
                        <th class="py-3 px-4 text-center">Rekomendasi Beli</th>
                        <th class="py-3 px-4 text-right">Estimasi Biaya</th>
                        <th class="py-3 px-4">Status Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($forecasts as $f)
                        <tr class="hover:bg-slate-50 {{ $f['rekomendasi_pembelian'] > 0 ? 'bg-emerald-50/20' : '' }}">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $f['nama_barang'] }}
                                <span class="text-[10px] text-slate-400 font-mono block">{{ $f['kode_barang'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                {{ $f['rata_rata_harian'] }} <span class="text-[10px] text-slate-400">{{ $f['satuan'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                {{ $f['kebutuhan_periode'] }} <span class="text-[10px] text-slate-400">{{ $f['satuan'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                {{ number_format($f['stok_saat_ini'], 0, ',', '.') }} {{ $f['satuan'] }}
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500">
                                {{ number_format($f['stok_minimum'], 0, ',', '.') }} {{ $f['satuan'] }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($f['rekomendasi_pembelian'] > 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black bg-brand-100 text-brand-800">
                                        +{{ number_format($f['rekomendasi_pembelian'], 0, ',', '.') }} {{ $f['satuan'] }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">
                                        Cukup (0)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($f['estimasi_biaya'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($f['has_enough_data'])
                                    <span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Aktif Terjual</span>
                                    </span>
                                @else
                                    <span class="text-[11px] text-amber-700 font-medium">
                                        Data penjualan minim
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada data barang untuk diprediksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
