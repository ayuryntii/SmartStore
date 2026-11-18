<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ ucwords(str_replace('_', ' ', $jenis)) }} - BAWARA</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 15px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .logo-title {
            font-size: 20px;
            font-weight: bold;
            color: #065f46;
            margin-bottom: 3px;
            letter-spacing: 1px;
        }
        .subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .period {
            font-size: 10px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #d1d5db;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }
        .kpi-card {
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-title">BAWARA</div>
        <div class="subtitle">Sistem Informasi Manajemen Persediaan & Operasional Warung</div>
        <div class="report-title">LAPORAN {{ strtoupper(str_replace('_', ' ', $jenis)) }}</div>
        @if(!in_array($jenis, ['barang', 'stok_saat_ini', 'stok_minimum', 'barang_habis', 'barang_kedaluwarsa']))
            <div class="period">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</div>
        @endif
        <div class="period">Dicetak pada: {{ date('d/m/Y H:i') }} WIB</div>
    </div>

    @if($jenis == 'keuangan_sederhana')
        <div class="kpi-card">
            <h3 style="margin: 0 0 10px 0; color: #065f46; font-size: 12px;">RINGKASAN KEUANGAN OPERASIONAL</h3>
            <table>
                <tr>
                    <td style="width: 70%;">Total Penjualan Kotor</td>
                    <td class="text-right font-bold">Rp {{ number_format($data['total_penjualan_kotor'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Diskon Diberikan</td>
                    <td class="text-right" style="color: #dc2626;">- Rp {{ number_format($data['total_diskon'], 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #ecfdf5;">
                    <td class="font-bold">Total Penjualan Bersih (Omzet)</td>
                    <td class="text-right font-bold" style="color: #065f46;">Rp {{ number_format($data['total_penjualan_bersih'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Penerimaan Kas Aktual (Kas Masuk)</td>
                    <td class="text-right font-bold">Rp {{ number_format($data['total_kas_masuk'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Pembelian Stok (Kas Keluar)</td>
                    <td class="text-right" style="color: #dc2626;">Rp {{ number_format($data['total_pembelian'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Piutang Belum Tertagih</td>
                    <td class="text-right" style="color: #d97706;">Rp {{ number_format($data['total_piutang_belum_dibayar'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Estimasi Harga Pokok Penjualan (HPP)</td>
                    <td class="text-right">Rp {{ number_format($data['total_hpp'], 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f3f4f6;">
                    <td class="font-bold">ESTIMASI LABA KOTOR</td>
                    <td class="text-right font-bold" style="font-size: 12px; color: #059669;">Rp {{ number_format($data['estimasi_laba_kotor'], 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f3f4f6;">
                    <td class="font-bold">ARUS KAS BERSIH</td>
                    <td class="text-right font-bold" style="font-size: 12px;">Rp {{ number_format($data['arus_kas_bersih'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

    @elseif($jenis == 'penjualan')
        <table>
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Kasir</th>
                    <th class="text-right">Tagihan</th>
                    <th class="text-right">Bayar</th>
                    <th class="text-right">Sisa Utang</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td class="font-bold">{{ $row->nomor_faktur }}</td>
                    <td>{{ $row->tanggal_penjualan->format('d/m/Y H:i') }}</td>
                    <td>{{ $row->pelanggan?->nama_pelanggan ?? 'Umum' }}</td>
                    <td>{{ $row->pengguna?->nama ?? '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $row->sisa_tagihan > 0 ? 'Rp ' . number_format($row->sisa_tagihan, 0, ',', '.') : '-' }}</td>
                    <td class="text-center">{{ $row->status_pembayaran_label }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">Tidak ada transaksi penjualan pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($jenis == 'pembelian')
        <table>
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Pemasok</th>
                    <th>Petugas</th>
                    <th class="text-right">Total Pembelian</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td class="font-bold">{{ $row->nomor_faktur }}</td>
                    <td>{{ $row->tanggal_pembelian->format('d/m/Y H:i') }}</td>
                    <td>{{ $row->pemasok?->nama_pemasok ?? '-' }}</td>
                    <td>{{ $row->pengguna?->nama ?? '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($row->total_pembelian, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($row->status_pembelian) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada transaksi pembelian pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif(in_array($jenis, ['barang', 'stok_saat_ini', 'stok_minimum', 'barang_habis']))
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th class="text-right">Harga Beli</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-center">Stok Min</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td>{{ $row->kode_barang }}</td>
                    <td class="font-bold">{{ $row->nama_barang }}</td>
                    <td>{{ $row->kategori?->nama_kategori ?? '-' }}</td>
                    <td>{{ $row->satuan?->nama_satuan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($row->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row->stok_minimum }}</td>
                    <td class="text-center font-bold">{{ $row->stok_saat_ini }}</td>
                    <td class="text-center">{{ $row->status_stok_label }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center">Tidak ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif(in_array($jenis, ['barang_masuk', 'barang_keluar']))
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jenis Gerakan</th>
                    <th class="text-center">Jumlah</th>
                    <th>No. Batch</th>
                    <th>Petugas</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td>{{ $row->tanggal_pergerakan ? $row->tanggal_pergerakan->format('d/m/Y H:i') : ($row->dibuat_pada ? $row->dibuat_pada->format('d/m/Y H:i') : '-') }}</td>
                    <td class="font-bold">{{ $row->barang?->nama_barang }} ({{ $row->barang?->kode_barang }})</td>
                    <td>{{ $row->jenis_pergerakan_label }}</td>
                    <td class="text-center font-bold">{{ $row->jumlah > 0 ? '+' : '' }}{{ $row->jumlah }} {{ $row->barang?->satuan?->singkatan }}</td>
                    <td>{{ $row->batch?->nomor_batch ?? '-' }}</td>
                    <td>{{ $row->pengguna?->nama ?? '-' }}</td>
                    <td>{{ $row->catatan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">Tidak ada riwayat mutasi stok.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($jenis == 'piutang_pelanggan')
        <table>
            <thead>
                <tr>
                    <th>Faktur</th>
                    <th>Tgl Transaksi</th>
                    <th>Jatuh Tempo</th>
                    <th>Pelanggan</th>
                    <th class="text-right">Tagihan</th>
                    <th class="text-right">Sudah Dibayar</th>
                    <th class="text-right">Sisa Utang</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td class="font-bold">{{ $row->nomor_faktur }}</td>
                    <td>{{ $row->tanggal_penjualan->format('d/m/Y') }}</td>
                    <td>{{ $row->tanggal_jatuh_tempo ? $row->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</td>
                    <td class="font-bold">{{ $row->pelanggan?->nama_pelanggan ?? 'Umum' }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #dc2626;">Rp {{ number_format($row->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row->status_pembayaran_label }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">Tidak ada piutang tertunggak.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($jenis == 'barang_terlaris')
        <table>
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Kode & Nama Barang</th>
                    <th class="text-center">Total Terjual</th>
                    <th class="text-right">Total Omzet</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $row->nama_barang }} ({{ $row->kode_barang }})</td>
                    <td class="text-center font-bold">{{ $row->total_qty }} unit</td>
                    <td class="text-right font-bold">Rp {{ number_format($row->total_omset, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Belum ada data penjualan produk.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($jenis == 'barang_sering_habis')
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Frekuensi Habis</th>
                    <th class="text-center">Stok Saat Ini</th>
                    <th class="text-center">Rata-rata Durasi Kosong</th>
                    <th class="text-right">Terakhir Habis</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td>{{ $row->barang?->kode_barang }}</td>
                    <td class="font-bold">{{ $row->barang?->nama_barang }}</td>
                    <td class="text-center font-bold">{{ $row->total_kejadian }} kali</td>
                    <td class="text-center">{{ $row->barang?->stok_saat_ini ?? 0 }}</td>
                    <td class="text-center">{{ round($row->rata_rata_jam_kosong ?? 0, 1) }} jam</td>
                    <td class="text-right">{{ \Carbon\Carbon::parse($row->terakhir_habis)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada riwayat barang habis.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($jenis == 'barang_kedaluwarsa')
        <table>
            <thead>
                <tr>
                    <th>No. Batch</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Tgl Kedaluwarsa</th>
                    <th class="text-center">Sisa Hari</th>
                    <th class="text-center">Stok Batch</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td>{{ $row->nomor_batch }}</td>
                    <td class="font-bold">{{ $row->barang?->nama_barang }}</td>
                    <td class="text-center">{{ $row->tanggal_kedaluwarsa->format('d/m/Y') }}</td>
                    <td class="text-center font-bold">{{ $row->sisa_hari <= 0 ? 'Expired' : $row->sisa_hari . ' hari' }}</td>
                    <td class="text-center font-bold">{{ $row->stok_saat_ini }}</td>
                    <td class="text-center">{{ $row->sisa_hari <= 0 ? 'Kedaluwarsa' : 'Mendekati ED' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada batch yang kedaluwarsa.</td></tr>
                @endforelse
            </tbody>
        </table>

    @endif

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi BAWARA<br>
        Penanggung Jawab Operasional Warung
    </div>

</body>
</html>
