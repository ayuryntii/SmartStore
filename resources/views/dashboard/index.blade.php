@extends('layouts.app', ['title' => 'Dashboard', 'breadcrumb' => 'Overview & Statistik'])

@section('content')
<div class="space-y-8">
    <!-- Top Greeting Banner (Soft Aesthetic Palette) -->
    <div class="bg-gradient-to-r from-[#EBF3E5] via-[#DFECD7] to-[#D3E4C9] border border-[#BFD5AE] rounded-3xl p-6 sm:p-8 text-[#24362D] shadow-sm relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#76944C]/15 text-[#3D5523] border border-[#76944C]/30 mb-3">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#76944C]"></i>
                <span>Sistem Operasional Warung Modern</span>
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-[#24362D] tracking-tight">
                Halo, {{ auth()->user()->nama }}! 👋
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-[#4E6146] font-medium leading-relaxed">
                Pantau ketersediaan persediaan barang sembako, arus kas operasional penjualan, dan perencanaan belanja secara akurat dan terintegrasi.
            </p>
        </div>
        <div class="absolute right-6 top-1/2 -translate-y-1/2 opacity-15 pointer-events-none hidden sm:block text-[#76944C]">
            <i data-lucide="store" class="w-32 h-32"></i>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- 10 KPI STATS CARDS -->
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
            <i data-lucide="activity" class="w-4 h-4"></i>
            <span>Ringkasan Metrik Utama Admin</span>
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 1. Total Jenis Barang -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Jenis Barang</span>
                    <div class="p-2 rounded-xl bg-slate-100 text-slate-700"><i data-lucide="package" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($totalJenisBarang, 0, ',', '.') }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Item aktif terdaftar</div>
            </div>

            <!-- 2. Total Stok Barang -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Total Unit Stok</span>
                    <div class="p-2 rounded-xl bg-indigo-50 text-indigo-700"><i data-lucide="boxes" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-indigo-900">{{ number_format($totalStokBarang, 0, ',', '.') }}</div>
                <div class="text-[11px] text-indigo-600/80 mt-1">Total fisik tersimpan</div>
            </div>

            <!-- 3. Stok Aman -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Stok Aman</span>
                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-700"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($barangAman, 0, ',', '.') }}</div>
                <div class="text-[11px] text-emerald-600/80 mt-1">&gt; Stok Minimum</div>
            </div>

            <!-- 4. Stok Menipis -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Stok Menipis</span>
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-700"><i data-lucide="alert-triangle" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-amber-600">{{ number_format($barangMenipisCount, 0, ',', '.') }}</div>
                <div class="text-[11px] text-amber-600/80 mt-1">&le; Stok Minimum</div>
            </div>

            <!-- 5. Stok Habis -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Barang Habis</span>
                    <div class="p-2 rounded-xl bg-rose-50 text-rose-700"><i data-lucide="x-circle" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-rose-600">{{ number_format($barangHabisCount, 0, ',', '.') }}</div>
                <div class="text-[11px] text-rose-600/80 mt-1">Stok = 0</div>
            </div>

            <!-- 6. Total Penjualan Hari Ini -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Penjualan Hari Ini</span>
                    <div class="p-2 rounded-xl bg-brand-50 text-brand-700"><i data-lucide="badge-dollar-sign" class="w-4 h-4"></i></div>
                </div>
                <div class="text-xl font-extrabold text-brand-800">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</div>
                <div class="text-[11px] text-brand-600/80 mt-1">Hari ini (selesai)</div>
            </div>

            <!-- 7. Total Transaksi Penjualan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Total Transaksi</span>
                    <div class="p-2 rounded-xl bg-blue-50 text-blue-700"><i data-lucide="shopping-bag" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-blue-900">{{ number_format($totalTransaksiPenjualan, 0, ',', '.') }}</div>
                <div class="text-[11px] text-blue-600/80 mt-1">Faktur penjualan</div>
            </div>

            <!-- 8. Total Pembelian -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Total Pembelian</span>
                    <div class="p-2 rounded-xl bg-purple-50 text-purple-700"><i data-lucide="truck" class="w-4 h-4"></i></div>
                </div>
                <div class="text-xl font-extrabold text-purple-900">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</div>
                <div class="text-[11px] text-purple-600/80 mt-1">Belanja barang masuk</div>
            </div>

            <!-- 9. Total Piutang Pelanggan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Total Piutang</span>
                    <div class="p-2 rounded-xl bg-orange-50 text-orange-700"><i data-lucide="receipt" class="w-4 h-4"></i></div>
                </div>
                <div class="text-xl font-extrabold text-orange-800">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
                <div class="text-[11px] text-orange-600/80 mt-1">Utang belum terbayar</div>
            </div>

            <!-- 10. Rencana Belanja Aktif -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Rencana Belanja</span>
                    <div class="p-2 rounded-xl bg-teal-50 text-teal-700"><i data-lucide="clipboard-check" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-teal-900">{{ number_format($rencanaBelanjaAktif, 0, ',', '.') }}</div>
                <div class="text-[11px] text-teal-600/80 mt-1">Draf / dipesan</div>
            </div>
        </div>
    </div>

    <!-- VISUALISASI GRAFIK ANALITIK -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grafik Penjualan Harian -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Grafik Penjualan 7 Hari Terakhir</h3>
                    <p class="text-xs text-slate-400">Tren nominal transaksi penjualan harian (Rp)</p>
                </div>
                <div class="p-2 rounded-xl bg-brand-50 text-brand-700"><i data-lucide="trending-up" class="w-4 h-4"></i></div>
            </div>
            <div class="h-64">
                <canvas id="chartHarian"></canvas>
            </div>
        </div>

        <!-- Grafik Penjualan Bulanan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Grafik Penjualan 6 Bulan Terakhir</h3>
                    <p class="text-xs text-slate-400">Akumulasi pergerakan omzet per bulan</p>
                </div>
                <div class="p-2 rounded-xl bg-blue-50 text-blue-700"><i data-lucide="calendar" class="w-4 h-4"></i></div>
            </div>
            <div class="h-64">
                <canvas id="chartBulanan"></canvas>
            </div>
        </div>

        <!-- Grafik Top 5 Barang Terlaris -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Barang Paling Banyak Terjual</h3>
                    <p class="text-xs text-slate-400">Top produk terlaris berdasarkan kuantitas transaksi</p>
                </div>
                <div class="p-2 rounded-xl bg-amber-50 text-amber-700"><i data-lucide="award" class="w-4 h-4"></i></div>
            </div>
            <div class="h-64">
                <canvas id="chartTopProducts"></canvas>
            </div>
        </div>

        <!-- Grafik Barang Paling Sering Habis & Status Piutang -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Status Pembayaran Piutang Penjualan</h3>
                    <p class="text-xs text-slate-400">Rasio transaksi lunas vs belum lunas</p>
                </div>
                <div class="p-2 rounded-xl bg-rose-50 text-rose-700"><i data-lucide="pie-chart" class="w-4 h-4"></i></div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="chartPiutang"></canvas>
            </div>
        </div>
    </div>

    <!-- BAGIAN ALERT PERINGATAN: STOK MENIPIS & BARANG HABIS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daftar Barang Habis -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Daftar Barang Habis (Stok 0)</h4>
                        <p class="text-xs text-slate-500">Perlu segera dilakukan pengadaan belanja</p>
                    </div>
                </div>
                <a href="{{ route('rencana-belanja.create') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 flex items-center gap-1">
                    <span>Buat Rencana</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            <div class="p-5 flex-1 overflow-x-auto">
                @if($daftarBarangHabis->count() > 0)
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100">
                                <th class="pb-2 font-semibold">Kode</th>
                                <th class="pb-2 font-semibold">Nama Barang</th>
                                <th class="pb-2 font-semibold">Kategori</th>
                                <th class="pb-2 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($daftarBarangHabis as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 font-mono text-slate-500">{{ $item->kode_barang }}</td>
                                    <td class="py-2.5 font-bold text-slate-800">{{ $item->nama_barang }}</td>
                                    <td class="py-2.5 text-slate-500">{{ $item->kategori?->nama_kategori }}</td>
                                    <td class="py-2.5 text-right">
                                        <button type="button"
                                            onclick="openModalLaporHabis('{{ $item->id }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->stok_saat_ini }}')"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200">
                                            Catat Laporan
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                        <p class="text-xs font-semibold">Semua persediaan barang dalam kondisi tersedia!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-amber-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Peringatan Stok Menipis</h4>
                        <p class="text-xs text-slate-500">Stok tersisa berada di bawah atau sama dengan stok minimum</p>
                    </div>
                </div>
                <a href="{{ route('stok.index', ['status_stok' => 'menipis']) }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            <div class="p-5 flex-1 overflow-x-auto">
                @if($daftarBarangMenipis->count() > 0)
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100">
                                <th class="pb-2 font-semibold">Nama Barang</th>
                                <th class="pb-2 font-semibold">Sisa Stok</th>
                                <th class="pb-2 font-semibold">Batas Minimum</th>
                                <th class="pb-2 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($daftarBarangMenipis as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 font-bold text-slate-800">{{ $item->nama_barang }}</td>
                                    <td class="py-2.5 font-extrabold text-amber-600">
                                        {{ number_format($item->stok_saat_ini, 0, ',', '.') }} {{ $item->satuan?->singkatan }}
                                    </td>
                                    <td class="py-2.5 text-slate-500">
                                        {{ number_format($item->stok_minimum, 0, ',', '.') }} {{ $item->satuan?->singkatan }}
                                    </td>
                                    <td class="py-2.5 text-right">
                                        <button type="button"
                                            onclick="openModalLaporHabis('{{ $item->id }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->stok_saat_ini }}')"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200">
                                            Lapor Habis
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <i data-lucide="shield-check" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                        <p class="text-xs font-semibold">Tidak ada barang yang berada di batas menipis.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PREDIKSI KEBUTUHAN STOK (MOVING AVERAGE) - ROLE ADMIN -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#76944C]/15 text-[#76944C] flex items-center justify-center font-bold">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-base font-extrabold text-slate-900">Prediksi Kebutuhan Belanja (Moving Average)</h4>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FFD21F]/25 text-[#524100] border border-[#FFD21F]/40">
                            Rekomendasi 7 Hari
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">Estimasi otomatis kuantitas belanja berdasarkan riwayat penjualan (30 hari) untuk mencegah kehabisan stok</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('analisis.prediksi') }}" class="px-3 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition flex items-center gap-1.5">
                    <i data-lucide="bar-chart" class="w-3.5 h-3.5"></i>
                    <span>Analisis Lengkap</span>
                </a>
                <a href="{{ route('rencana-belanja.create') }}" class="px-3.5 py-1.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>Buat Rencana Belanja</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Item Perlu Restock</span>
                <span class="text-2xl font-black text-brand-700">{{ $totalItemPerluBeli }} Barang</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Direkomendasikan segera dipesan</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Estimasi Total Anggaran</span>
                <span class="text-2xl font-black text-slate-900">Rp {{ number_format($totalEstimasiBiayaPrediksi, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Berdasarkan HPP / harga beli terakhir</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Metode Analisis</span>
                <span class="text-base font-bold text-[#76944C] block mt-1">Simple Moving Average</span>
                <span class="text-[11px] text-slate-500 block mt-0.5">Riwayat 30 Hari &bull; Siklus 7 Hari</span>
            </div>
        </div>

        @if($topPrediksiKebutuhan->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-500 font-semibold border-b border-slate-100">
                            <th class="py-2.5 px-3">Nama Barang</th>
                            <th class="py-2.5 px-3 text-center">Stok Saat Ini</th>
                            <th class="py-2.5 px-3 text-center">Rata-rata Terjual/Hari</th>
                            <th class="py-2.5 px-3 text-center">Kebutuhan 7 Hari</th>
                            <th class="py-2.5 px-3 text-right">Rekomendasi Beli</th>
                            <th class="py-2.5 px-3 text-right">Estimasi Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($topPrediksiKebutuhan as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-2.5 px-3">
                                    <span class="font-bold text-slate-900 block">{{ $item['nama_barang'] }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $item['kode_barang'] }} &bull; {{ $item['kategori'] }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-700">
                                    {{ number_format($item['stok_saat_ini'], 0, ',', '.') }} {{ $item['satuan'] }}
                                </td>
                                <td class="py-2.5 px-3 text-center text-slate-600">
                                    {{ $item['rata_rata_harian'] }} {{ $item['satuan'] }}/hari
                                </td>
                                <td class="py-2.5 px-3 text-center text-slate-700 font-semibold">
                                    {{ $item['kebutuhan_periode'] }} {{ $item['satuan'] }}
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black bg-[#C8DAA6] text-[#243317] border border-[#76944C]/30">
                                        +{{ number_format($item['rekomendasi_pembelian'], 0, ',', '.') }} {{ $item['satuan'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-slate-900">
                                    Rp {{ number_format($item['estimasi_biaya'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-6 text-slate-400 text-xs">
                <i data-lucide="check-circle" class="w-8 h-8 text-[#76944C] mx-auto mb-2"></i>
                <p class="font-semibold text-slate-700">Persediaan Barang Aman!</p>
                <p class="text-[11px] text-slate-500">Semua barang diperkirakan memiliki stok yang aman untuk 7 hari ke depan.</p>
            </div>
        @endif
    </div>

    <!-- BAGIAN PIUTANG JATUH TEMPO & AKTIVITAS TERBARU -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Utang Lewat Jatuh Tempo -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold">
                        <i data-lucide="calendar-x" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Utang Pelanggan Melewati Jatuh Tempo</h4>
                        <p class="text-xs text-slate-500">Daftar tagihan yang memerlukan follow-up segera</p>
                    </div>
                </div>
                <a href="{{ route('piutang.index', ['status_pembayaran' => 'jatuh_tempo']) }}" class="text-xs font-bold text-brand-700 hover:text-brand-800">
                    Kelola Piutang &rarr;
                </a>
            </div>

            @if($utangJatuhTempo->count() > 0)
                <div class="space-y-3">
                    @foreach($utangJatuhTempo as $debt)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-800">{{ $debt->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum' }}</div>
                                <div class="text-[11px] text-slate-400">Faktur: {{ $debt->nomor_faktur }} &bull; Jatuh Tempo: {{ $debt->tanggal_jatuh_tempo->format('d/m/Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold text-xs text-rose-600">Rp {{ number_format($debt->sisa_tagihan, 0, ',', '.') }}</div>
                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                                    Terlambat
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-400">
                    <i data-lucide="thumbs-up" class="w-7 h-7 text-emerald-400 mx-auto mb-2"></i>
                    <p class="text-xs font-semibold">Tidak ada utang yang melewati jatuh tempo saat ini.</p>
                </div>
            @endif
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                        <i data-lucide="history" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Riwayat Aktivitas Sistem Terkini</h4>
                        <p class="text-xs text-slate-500">Audit trail operasi pengguna</p>
                    </div>
                </div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('aktivitas.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800">
                        Audit Log &rarr;
                    </a>
                @endif
            </div>

            @if($aktivitasTerbaru->count() > 0)
                <div class="space-y-3">
                    @foreach($aktivitasTerbaru as $akt)
                        <div class="flex items-start gap-3 text-xs border-b border-slate-100 pb-2.5 last:border-0 last:pb-0">
                            <div class="w-2 h-2 rounded-full bg-brand-500 mt-1.5 shrink-0"></div>
                            <div class="flex-1">
                                <span class="font-bold text-slate-800">{{ $akt->pengguna?->nama ?? 'Sistem' }}</span>
                                <span class="text-slate-600 font-medium"> - {{ $akt->deskripsi }}</span>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $akt->dibuat_pada->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-400 text-xs">Belum ada riwayat aktivitas yang tercatat.</div>
            @endif
        </div>
    </div>
    @else
    <!-- DASHBOARD KASIR (TRANSAKSI & PEMBAYARAN) -->
    <!-- Quick Action Shortcut Cards: Pilihan Transaksi Tunai vs Utang -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Transaksi Tunai -->
        <a href="{{ route('penjualan.create', ['tipe' => 'tunai']) }}"
           class="p-5 bg-[#76944C] hover:bg-[#688442] text-white rounded-2xl shadow-sm transition flex items-center justify-between group">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-[#E8F2DC]">POS &bull; Bayar Tunai</span>
                <h4 class="text-lg font-black mt-0.5">Transaksi Tunai</h4>
                <p class="text-xs text-[#E8F2DC]/90 mt-1">Pembayaran tunai uang pas / lunas</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center group-hover:scale-110 transition shrink-0">
                <i data-lucide="banknote" class="w-6 h-6"></i>
            </div>
        </a>

        <!-- 2. Transaksi Utang / Tempo -->
        <a href="{{ route('penjualan.create', ['tipe' => 'utang']) }}"
           class="p-5 bg-[#E5BE4B] hover:bg-[#D9B23E] text-[#3A2D05] rounded-2xl shadow-sm transition flex items-center justify-between group">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-[#574308]">POS &bull; Bayar Utang</span>
                <h4 class="text-lg font-black mt-0.5">Transaksi Utang</h4>
                <p class="text-xs text-[#574308]/90 mt-1">Catat ke buku utang & tempo</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-black/10 flex items-center justify-center group-hover:scale-110 transition shrink-0">
                <i data-lucide="book-open" class="w-6 h-6"></i>
            </div>
        </a>

        <!-- 3. Pelunasan Utang Pelanggan -->
        <a href="{{ route('piutang.index') }}"
           class="p-5 bg-[#557D68] hover:bg-[#486B58] text-white rounded-2xl shadow-sm transition flex items-center justify-between group">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-[#E3EDE7]">Buku Utang</span>
                <h4 class="text-lg font-black mt-0.5">Catat Cicilan</h4>
                <p class="text-xs text-[#E3EDE7]/90 mt-1">Terima pelunasan piutang pembeli</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center group-hover:scale-110 transition shrink-0">
                <i data-lucide="credit-card" class="w-6 h-6"></i>
            </div>
        </a>

        <!-- 4. Informasi Barang -->
        <a href="{{ route('barang.index') }}"
           class="p-5 bg-white hover:bg-slate-50 text-slate-900 border border-slate-200 rounded-2xl shadow-sm transition flex items-center justify-between group">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-slate-500">Katalog Barang</span>
                <h4 class="text-lg font-bold mt-0.5">Cek Info Barang</h4>
                <p class="text-xs text-slate-500 mt-1">Lihat harga jual & stok fisik rak</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center group-hover:scale-110 transition shrink-0 text-slate-700">
                <i data-lucide="package-search" class="w-6 h-6"></i>
            </div>
        </a>
    </div>

    <!-- Ringkasan Operasional Kasir Hari Ini -->
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            <span>Ringkasan Transaksi Kasir Hari Ini ({{ \Carbon\Carbon::today()->translatedFormat('d F Y') }})</span>
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Total Transaksi</span>
                    <div class="p-2 rounded-xl bg-blue-50 text-blue-700"><i data-lucide="receipt" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($penjualanHariIniCount, 0, ',', '.') }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Struk penjualan hari ini</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Total Omzet Penjualan</span>
                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-700"><i data-lucide="trending-up" class="w-4 h-4"></i></div>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-brand-700">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</div>
                <div class="text-[11px] text-emerald-600 mt-1">Nilai barang terjual hari ini</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Kas Tunai Masuk</span>
                    <div class="p-2 rounded-xl bg-indigo-50 text-indigo-700"><i data-lucide="banknote" class="w-4 h-4"></i></div>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-indigo-700">Rp {{ number_format($kasMasukHariIni, 0, ',', '.') }}</div>
                <div class="text-[11px] text-indigo-600 mt-1">Uang fisik kasir yang diterima</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold">Transaksi Utang / Tempo</span>
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-700"><i data-lucide="clock" class="w-4 h-4"></i></div>
                </div>
                <div class="text-2xl font-extrabold text-amber-600">{{ number_format($transaksiUtangHariIniCount, 0, ',', '.') }}</div>
                <div class="text-[11px] text-amber-600 mt-1">Tagihan tempo hari ini</div>
            </div>
        </div>
    </div>

    <!-- Tables for Kasir -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaksi Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-emerald-600"></i>
                    Transaksi Kasir Terakhir
                </h3>
                <a href="{{ route('penjualan.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="pb-2">Faktur</th>
                            <th class="pb-2">Pelanggan</th>
                            <th class="pb-2 text-right">Tagihan</th>
                            <th class="pb-2 text-center">Status</th>
                            <th class="pb-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiTerbaruHariIni as $tr)
                        <tr>
                            <td class="py-2.5 font-mono font-medium text-slate-800">{{ $tr->nomor_faktur }}</td>
                            <td class="py-2.5 font-medium">{{ $tr->pelanggan?->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="py-2.5 text-right font-bold text-slate-900">Rp {{ number_format($tr->total_tagihan, 0, ',', '.') }}</td>
                            <td class="py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $tr->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $tr->status_pembayaran_label }}
                                </span>
                            </td>
                            <td class="py-2.5 text-right">
                                <a href="{{ route('penjualan.print', $tr) }}" target="_blank" class="p-1 text-slate-400 hover:text-brand-700 inline-block" title="Cetak Struk">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-4 text-center text-slate-400">Belum ada transaksi hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Utang Pelanggan Tertunggak / Jatuh Tempo -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                    Piutang Pelanggan (Perlu Ditagih)
                </h3>
                <a href="{{ route('piutang.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800">
                    Buku Utang &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="pb-2">Faktur</th>
                            <th class="pb-2">Pelanggan</th>
                            <th class="pb-2 text-right">Sisa Utang</th>
                            <th class="pb-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($utangJatuhTempo as $uj)
                        <tr>
                            <td class="py-2.5 font-mono font-medium text-slate-800">
                                <a href="{{ route('penjualan.show', $uj->id) }}" class="font-bold hover:text-brand-700">
                                    {{ $uj->nomor_faktur }}
                                </a>
                                @if($uj->tanggal_jatuh_tempo)
                                    <div class="text-[10px] text-slate-400">
                                        Tempo: {{ $uj->tanggal_jatuh_tempo->format('d/m/Y') }}
                                        @if($uj->tanggal_jatuh_tempo->isPast())
                                            <span class="text-[9px] px-1 rounded bg-rose-100 text-rose-700 font-bold ml-0.5">Lewat</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-2.5 font-bold text-slate-900">{{ $uj->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum' }}</td>
                            <td class="py-2.5 text-right font-black text-rose-600">Rp {{ number_format($uj->sisa_tagihan, 0, ',', '.') }}</td>
                            <td class="py-2.5 text-right">
                                <a href="{{ route('piutang.show', $uj->pelanggan_id) }}" class="px-2.5 py-1 bg-[#76944C] hover:bg-[#688442] text-white rounded-lg text-[10px] font-bold shadow-sm transition">
                                    Bayar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-4 text-center text-slate-400">Tidak ada utang yang mendekati atau melewati jatuh tempo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if(auth()->user()->isAdmin())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Harian
        const ctxHarian = document.getElementById('chartHarian').getContext('2d');
        new Chart(ctxHarian, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartHarianLabels) !!},
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: {!! json_encode($chartHarianData) !!},
                    borderColor: '#76944C',
                    backgroundColor: 'rgba(118, 148, 76, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#76944C',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) { return 'Rp ' + Number(value).toLocaleString('id-ID'); }
                        }
                    }
                }
            }
        });

        // 2. Chart Bulanan
        const ctxBulanan = document.getElementById('chartBulanan').getContext('2d');
        new Chart(ctxBulanan, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartBulananLabels) !!},
                datasets: [{
                    label: 'Omzet Bulanan (Rp)',
                    data: {!! json_encode($chartBulananData) !!},
                    backgroundColor: '#557D68',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) { return 'Rp ' + Number(value).toLocaleString('id-ID'); }
                        }
                    }
                }
            }
        });

        // 3. Top Products
        const ctxTop = document.getElementById('chartTopProducts').getContext('2d');
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: {!! json_encode(!empty($topProductLabels) ? $topProductLabels : ['Belum Ada Data']) !!},
                datasets: [{
                    label: 'Qty Terjual',
                    data: {!! json_encode(!empty($topProductData) ? $topProductData : [0]) !!},
                    backgroundColor: '#8FB060',
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // 4. Status Piutang Donut
        const ctxPiutang = document.getElementById('chartPiutang').getContext('2d');
        new Chart(ctxPiutang, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Belum Lunas / Sebagian'],
                datasets: [{
                    data: [{{ $penjualanLunasCount }}, {{ $penjualanBelumLunasCount }}],
                    backgroundColor: ['#76944C', '#FFD21F'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endif
@endpush
