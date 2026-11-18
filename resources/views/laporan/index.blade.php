@extends('layouts.app')

@section('title', 'Pusat Laporan Lengkap')
@section('page-title', 'Pusat Laporan & Analitik')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="file-spreadsheet" class="w-6 h-6 text-emerald-600"></i>
                    Pusat Laporan Bisnis BAWARA
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Akses 15 jenis laporan komprehensif operasional, stok barang, penjualan, pembelian, piutang, dan laba kotor.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('laporan.pdf', request()->all()) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium shadow-xs transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Export PDF
                </a>
                <a href="{{ route('laporan.excel', request()->all()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-medium shadow-xs transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV / Excel
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('laporan.index') }}" method="GET" class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Jenis Laporan (15 Jenis)</label>
                <select name="jenis" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 font-medium">
                    <optgroup label="Laporan Stok & Master Barang">
                        <option value="barang" {{ $jenis == 'barang' ? 'selected' : '' }}>1. Laporan Data Barang</option>
                        <option value="stok_saat_ini" {{ $jenis == 'stok_saat_ini' ? 'selected' : '' }}>2. Laporan Stok Saat Ini</option>
                        <option value="stok_minimum" {{ $jenis == 'stok_minimum' ? 'selected' : '' }}>3. Laporan Stok Minimum (Menipis)</option>
                        <option value="barang_habis" {{ $jenis == 'barang_habis' ? 'selected' : '' }}>4. Laporan Barang Habis</option>
                        <option value="barang_masuk" {{ $jenis == 'barang_masuk' ? 'selected' : '' }}>5. Laporan Barang Masuk</option>
                        <option value="barang_keluar" {{ $jenis == 'barang_keluar' ? 'selected' : '' }}>6. Laporan Barang Keluar</option>
                    </optgroup>
                    <optgroup label="Laporan Penjualan & Pembelian">
                        <option value="penjualan" {{ $jenis == 'penjualan' ? 'selected' : '' }}>7. Laporan Penjualan</option>
                        <option value="pembelian" {{ $jenis == 'pembelian' ? 'selected' : '' }}>8. Laporan Pembelian</option>
                        <option value="rencana_belanja" {{ $jenis == 'rencana_belanja' ? 'selected' : '' }}>9. Laporan Rencana Belanja</option>
                    </optgroup>
                    <optgroup label="Laporan Piutang & Keuangan">
                        <option value="piutang_pelanggan" {{ $jenis == 'piutang_pelanggan' ? 'selected' : '' }}>10. Laporan Piutang Pelanggan</option>
                        <option value="pembayaran_utang" {{ $jenis == 'pembayaran_utang' ? 'selected' : '' }}>11. Laporan Riwayat Pembayaran Utang</option>
                        <option value="keuangan_sederhana" {{ $jenis == 'keuangan_sederhana' ? 'selected' : '' }}>12. Laporan Keuangan Sederhana</option>
                    </optgroup>
                    <optgroup label="Laporan Analisis & Kedaluwarsa">
                        <option value="barang_terlaris" {{ $jenis == 'barang_terlaris' ? 'selected' : '' }}>13. Laporan Barang Terlaris</option>
                        <option value="barang_sering_habis" {{ $jenis == 'barang_sering_habis' ? 'selected' : '' }}>14. Laporan Barang Sering Habis</option>
                        <option value="barang_kedaluwarsa" {{ $jenis == 'barang_kedaluwarsa' ? 'selected' : '' }}>15. Laporan Barang Kedaluwarsa</option>
                    </optgroup>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            @if(in_array($jenis, ['barang', 'stok_saat_ini', 'stok_minimum', 'barang_habis']))
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Kategori</label>
                <select name="kategori_id" class="w-full text-xs rounded-lg border-gray-300">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ $kategoriId == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(in_array($jenis, ['penjualan', 'piutang_pelanggan', 'pembayaran_utang']))
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Pelanggan</label>
                <select name="pelanggan_id" class="w-full text-xs rounded-lg border-gray-300">
                    <option value="">Semua Pelanggan</option>
                    @foreach($pelanggans as $p)
                        <option value="{{ $p->id }}" {{ $pelangganId == $p->id ? 'selected' : '' }}>{{ $p->nama_pelanggan }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($jenis == 'pembelian')
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Pemasok</label>
                <select name="pemasok_id" class="w-full text-xs rounded-lg border-gray-300">
                    <option value="">Semua Pemasok</option>
                    @foreach($pemasoks as $sup)
                        <option value="{{ $sup->id }}" {{ $pemasokId == $sup->id ? 'selected' : '' }}>{{ $sup->nama_pemasok }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-sm font-semibold text-gray-800">
                    Hasil: {{ strtoupper(str_replace('_', ' ', $jenis)) }}
                </span>
                @if(!in_array($jenis, ['barang', 'stok_saat_ini', 'stok_minimum', 'barang_habis', 'barang_kedaluwarsa']))
                    <span class="text-xs text-gray-500">({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</span>
                @endif
            </div>
            @if(is_countable($data))
                <span class="text-xs text-gray-500">Total baris: <strong>{{ count($data) }}</strong></span>
            @endif
        </div>

        @if($jenis == 'keuangan_sederhana')
            <!-- Financial Statement Card View -->
            <div class="p-6">
                <div class="max-w-3xl mx-auto space-y-4">
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                        <h3 class="text-sm font-bold text-emerald-900 uppercase tracking-wide">Ringkasan Pendapatan & Kas</h3>
                        <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                            <div class="p-3 bg-white rounded-lg border border-emerald-100">
                                <dt class="text-xs text-gray-500">Total Penjualan Kotor</dt>
                                <dd class="text-lg font-bold text-gray-900">Rp {{ number_format($data['total_penjualan_kotor'], 0, ',', '.') }}</dd>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-emerald-100">
                                <dt class="text-xs text-gray-500">Total Diskon Penjualan</dt>
                                <dd class="text-lg font-bold text-amber-600">- Rp {{ number_format($data['total_diskon'], 0, ',', '.') }}</dd>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-emerald-100">
                                <dt class="text-xs text-gray-500">Total Penjualan Bersih (Omzet)</dt>
                                <dd class="text-lg font-bold text-emerald-700">Rp {{ number_format($data['total_penjualan_bersih'], 0, ',', '.') }}</dd>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-emerald-100">
                                <dt class="text-xs text-gray-500">Total Kas Masuk Aktual</dt>
                                <dd class="text-lg font-bold text-blue-700">Rp {{ number_format($data['total_kas_masuk'], 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-5">
                        <h3 class="text-sm font-bold text-rose-900 uppercase tracking-wide">Beban & Pengeluaran</h3>
                        <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                            <div class="p-3 bg-white rounded-lg border border-rose-100">
                                <dt class="text-xs text-gray-500">Total Pembelian Stok (Kas Keluar)</dt>
                                <dd class="text-lg font-bold text-rose-700">Rp {{ number_format($data['total_pembelian'], 0, ',', '.') }}</dd>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-rose-100">
                                <dt class="text-xs text-gray-500">Estimasi HPP Barang Terjual</dt>
                                <dd class="text-lg font-bold text-gray-800">Rp {{ number_format($data['total_hpp'], 0, ',', '.') }}</dd>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-rose-100">
                                <dt class="text-xs text-gray-500">Total Piutang Belum Tertagih</dt>
                                <dd class="text-lg font-bold text-amber-700">Rp {{ number_format($data['total_piutang_belum_dibayar'], 0, ',', '.') }}</dd>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-rose-100">
                                <dt class="text-xs text-gray-500">Arus Kas Bersih (Kas Masuk - Beli)</dt>
                                <dd class="text-lg font-bold {{ $data['arus_kas_bersih'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    Rp {{ number_format($data['arus_kas_bersih'], 0, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-900 text-white rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Estimasi Laba Kotor (Penjualan Bersih - HPP)</p>
                            <p class="text-2xl font-bold text-emerald-400 mt-1">Rp {{ number_format($data['estimasi_laba_kotor'], 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-xs font-semibold">
                                Operasional Sehat
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($jenis == 'penjualan')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">No. Faktur</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3">Kasir</th>
                            <th class="px-4 py-3 text-right">Total Kotor</th>
                            <th class="px-4 py-3 text-right">Diskon</th>
                            <th class="px-4 py-3 text-right">Tagihan</th>
                            <th class="px-4 py-3 text-right">Bayar</th>
                            <th class="px-4 py-3 text-right">Sisa Utang</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-medium text-emerald-600 font-mono">{{ $row->nomor_faktur }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->tanggal_penjualan->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->pelanggan?->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $row->pengguna?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-600">Rp {{ number_format($row->diskon, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600 font-medium">Rp {{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ $row->sisa_tagihan > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                Rp {{ number_format($row->sisa_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $row->status_pembayaran == 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($row->status_pembayaran == 'sebagian' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $row->status_pembayaran_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="px-4 py-8 text-center text-gray-400">Tidak ada transaksi penjualan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($jenis == 'pembelian')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">No. Faktur</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Pemasok</th>
                            <th class="px-4 py-3">Petugas Gudang</th>
                            <th class="px-4 py-3 text-right">Total Pembelian</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-medium text-emerald-600 font-mono">{{ $row->nomor_faktur }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->tanggal_pembelian->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->pemasok?->nama_pemasok ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $row->pengguna?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($row->total_pembelian, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $row->status_pembelian == 'diterima' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($row->status_pembelian) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data pembelian pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif(in_array($jenis, ['barang', 'stok_saat_ini', 'stok_minimum', 'barang_habis']))
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Satuan</th>
                            <th class="px-4 py-3 text-right">Harga Beli</th>
                            <th class="px-4 py-3 text-right">Harga Jual</th>
                            <th class="px-4 py-3 text-center">Stok Min</th>
                            <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-mono text-xs font-medium text-gray-700">{{ $row->kode_barang }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->nama_barang }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->kategori?->nama_kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->satuan?->nama_satuan ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($row->harga_beli, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-700">Rp {{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">{{ $row->stok_minimum }}</td>
                            <td class="px-4 py-3 text-center font-bold {{ $row->stok_saat_ini == 0 ? 'text-rose-600' : ($row->stok_saat_ini <= $row->stok_minimum ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ $row->stok_saat_ini }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $row->status_stok == 'habis' ? 'bg-rose-100 text-rose-800' : ($row->status_stok == 'menipis' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $row->status_stok_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Tidak ada data barang yang memenuhi filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif(in_array($jenis, ['barang_masuk', 'barang_keluar']))
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kode & Nama Barang</th>
                            <th class="px-4 py-3">Jenis Gerakan</th>
                            <th class="px-4 py-3 text-center">Jumlah</th>
                            <th class="px-4 py-3">No. Batch</th>
                            <th class="px-4 py-3">Petugas</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs">{{ $row->tanggal_pergerakan ? $row->tanggal_pergerakan->format('d/m/Y H:i') : ($row->dibuat_pada ? $row->dibuat_pada->format('d/m/Y H:i') : '-') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $row->barang?->nama_barang }}</div>
                                <div class="text-xs font-mono text-gray-500">{{ $row->barang?->kode_barang }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $row->jenis_pergerakan_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold {{ $row->jumlah > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $row->jumlah > 0 ? '+' : '' }}{{ $row->jumlah }} {{ $row->barang?->satuan?->singkatan }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ $row->batch?->nomor_batch ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->pengguna?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $row->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada riwayat pergerakan stok pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($jenis == 'piutang_pelanggan')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">Faktur</th>
                            <th class="px-4 py-3">Tgl Transaksi</th>
                            <th class="px-4 py-3">Jatuh Tempo</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3 text-right">Total Tagihan</th>
                            <th class="px-4 py-3 text-right">Sudah Dibayar</th>
                            <th class="px-4 py-3 text-right">Sisa Piutang</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-mono font-medium text-emerald-600">{{ $row->nomor_faktur }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->tanggal_penjualan->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-xs {{ $row->tanggal_jatuh_tempo && $row->tanggal_jatuh_tempo->isPast() ? 'text-rose-600 font-bold' : 'text-gray-500' }}">
                                {{ $row->tanggal_jatuh_tempo ? $row->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->pelanggan?->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600">Rp {{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-rose-600">Rp {{ number_format($row->sisa_tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $row->status_pembayaran == 'sebagian' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $row->status_pembayaran_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada piutang pelanggan yang tertunggak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($jenis == 'pembayaran_utang')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">Tanggal Bayar</th>
                            <th class="px-4 py-3">No. Faktur</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3 text-right">Jumlah Dibayar</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3">Kasir / Penerima</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs">{{ $row->tanggal_bayar->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-mono text-emerald-600 font-medium">{{ $row->penjualan?->nomor_faktur }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->penjualan?->pelanggan?->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-700">Rp {{ number_format($row->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs uppercase">{{ $row->metode_pembayaran }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $row->pengguna?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $row->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada catatan pembayaran utang pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($jenis == 'barang_terlaris')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-center">Peringkat</th>
                            <th class="px-4 py-3">Kode & Nama Barang</th>
                            <th class="px-4 py-3 text-center">Total Terjual</th>
                            <th class="px-4 py-3 text-right">Total Pendapatan (Omzet)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $index => $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-center font-bold text-gray-500">#{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <div>{{ $row->nama_barang }}</div>
                                <div class="text-xs font-mono text-gray-400">{{ $row->kode_barang }}</div>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-emerald-700 text-base">{{ $row->total_qty }} unit</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($row->total_omset, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada data barang terjual pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($jenis == 'barang_sering_habis')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">Kode Barang</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3 text-center">Frekuensi Habis</th>
                            <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                            <th class="px-4 py-3 text-center">Rata-rata Durasi Habis</th>
                            <th class="px-4 py-3 text-right">Terakhir Habis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $row->barang?->kode_barang }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->barang?->nama_barang }}</td>
                            <td class="px-4 py-3 text-center font-bold text-rose-600">{{ $row->total_kejadian }} kali</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $row->barang?->stok_saat_ini ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">{{ round($row->rata_rata_jam_kosong ?? 0, 1) }} jam</td>
                            <td class="px-4 py-3 text-right text-xs text-gray-500">{{ \Carbon\Carbon::parse($row->terakhir_habis)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada riwayat barang habis pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($jenis == 'barang_kedaluwarsa')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">No. Batch</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3 text-center">Tgl Kedaluwarsa</th>
                            <th class="px-4 py-3 text-center">Sisa Hari</th>
                            <th class="px-4 py-3 text-center">Stok Batch</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-mono text-xs font-medium text-gray-700">{{ $row->nomor_batch }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->barang?->nama_barang }}</td>
                            <td class="px-4 py-3 text-center text-xs">{{ $row->tanggal_kedaluwarsa->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-xs font-semibold {{ $row->sisa_hari <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                {{ $row->sisa_hari <= 0 ? 'Expired' : $row->sisa_hari . ' hari lagi' }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-gray-800">{{ $row->stok_saat_ini }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $row->sisa_hari <= 0 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $row->sisa_hari <= 0 ? 'Kedaluwarsa' : 'Mendekati ED' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada batch barang yang kedaluwarsa atau mendekati masa ED (30 hari).</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($jenis == 'rencana_belanja')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">Nomor Rencana</th>
                            <th class="px-4 py-3">Tanggal Rencana</th>
                            <th class="px-4 py-3">Pembuat</th>
                            <th class="px-4 py-3 text-right">Estimasi Biaya</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-mono font-medium text-emerald-600">{{ $row->nomor_rencana }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->tanggal_rencana->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $row->pengguna?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($row->total_estimasi_biaya, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $row->status == 'selesai' ? 'bg-emerald-100 text-emerald-800' : ($row->status == 'disetujui' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $row->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data rencana belanja.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @endif
    </div>
</div>
@endsection
