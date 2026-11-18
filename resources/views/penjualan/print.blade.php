<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $penjualan->nomor_faktur }} - BAWARA</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 320px;
            margin: auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .border-t { border-top: 1px dashed #000; }
        .border-b { border-bottom: 1px dashed #000; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        .my-2 { margin-top: 8px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 3px 0; font-size: 11px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="no-print" style="margin-bottom: 15px; text-align: center;">
            <button onclick="window.print()" style="padding: 6px 16px; background: #059669; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Cetak Struk Sekarang
            </button>
            <button onclick="window.close()" style="padding: 6px 12px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; margin-left: 5px; cursor: pointer;">
                Tutup
            </button>
        </div>

        <div class="text-center">
            <div class="bold" style="font-size: 16px;">BAWARA</div>
            <div>Kelola Warung, Lebih Mudah</div>
            <div style="font-size: 10px;">Cirebon, Jawa Barat</div>
        </div>

        <div class="border-t border-b py-1 my-2" style="font-size: 10px;">
            <div>Faktur : {{ $penjualan->nomor_faktur }}</div>
            <div>Waktu  : {{ $penjualan->tanggal_penjualan->format('d/m/Y H:i') }}</div>
            <div>Kasir  : {{ $penjualan->pengguna?->nama ?? 'Kasir' }}</div>
            <div>Cust   : {{ $penjualan->pelanggan?->nama_pelanggan ?? 'Umum' }}</div>
        </div>

        <table>
            <thead>
                <tr class="border-b">
                    <th style="text-align: left;">Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->detailPenjualan as $det)
                    <tr>
                        <td colspan="3" class="bold">{{ $det->barang?->nama_barang }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 8px; color: #444;">@ {{ number_format($det->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($det->jumlah, 0, ',', '.') }}</td>
                        <td class="text-right bold">{{ number_format($det->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t my-2" style="padding-top: 5px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
                <div style="display: flex; justify-content: space-between;">
                    <span>Diskon:</span>
                    <span>-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="bold" style="display: flex; justify-content: space-between; font-size: 13px; margin: 4px 0;">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($penjualan->total_tagihan, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Bayar:</span>
                <span>Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->sisa_tagihan > 0)
                <div class="bold" style="display: flex; justify-content: space-between; color: #b91c1c;">
                    <span>SISA UTANG:</span>
                    <span>Rp {{ number_format($penjualan->sisa_tagihan, 0, ',', '.') }}</span>
                </div>
                @if($penjualan->tanggal_jatuh_tempo)
                    <div style="font-size: 9px; text-align: right; color: #b91c1c;">
                        Jatuh Tempo: {{ $penjualan->tanggal_jatuh_tempo->format('d/m/Y') }}
                    </div>
                @endif
            @else
                <div style="display: flex; justify-content: space-between;">
                    <span>Status:</span>
                    <span class="bold">LUNAS</span>
                </div>
            @endif
        </div>

        <div class="text-center border-t py-1 my-2" style="font-size: 10px;">
            <div>Terima Kasih Atas Kunjungan Anda!</div>
            <div>Barang yang dibeli tidak dapat ditukar</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            // auto trigger print
            // window.print();
        });
    </script>
</body>
</html>
