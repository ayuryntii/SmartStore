<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\PembayaranPenjualan;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SalesService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Generate unique sales invoice number: PJ-YYYYMMDD-XXXX
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'PJ-' . date('Ymd') . '-';
        $lastSale = Penjualan::where('nomor_faktur', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            $lastNum = (int) substr($lastSale->nomor_faktur, strlen($prefix));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    /**
     * Create a new sales transaction
     */
    public function createSale(array $data, ?int $penggunaId = null): Penjualan
    {
        return DB::transaction(function () use ($data, $penggunaId) {
            $userId = $penggunaId ?? Auth::id();

            if (empty($data['items']) || !is_array($data['items'])) {
                throw new InvalidArgumentException('Transaksi harus memiliki minimal satu barang.');
            }

            $nomorFaktur = !empty($data['nomor_faktur']) ? $data['nomor_faktur'] : $this->generateInvoiceNumber();
            $tanggalPenjualan = $data['tanggal_penjualan'] ?? now();
            $tanggalJatuhTempo = $data['tanggal_jatuh_tempo'] ?? null;
            $pelangganId = !empty($data['pelanggan_id']) ? (int) $data['pelanggan_id'] : null;
            $statusPenjualan = $data['status_penjualan'] ?? 'selesai';
            $diskon = max(0, (float) ($data['diskon'] ?? 0));
            $catatan = $data['catatan'] ?? null;
            $metodePembayaran = $data['metode_pembayaran'] ?? 'tunai';
            $jumlahBayarInput = max(0, (float) ($data['jumlah_bayar'] ?? 0));

            // Validate and calculate totals
            $totalHarga = 0;
            $processedItems = [];

            foreach ($data['items'] as $item) {
                $barangId = (int) $item['barang_id'];
                $jumlah = (float) $item['jumlah'];

                if ($jumlah <= 0) {
                    throw new InvalidArgumentException('Jumlah barang harus lebih dari 0.');
                }

                $barang = Barang::where('id', $barangId)->lockForUpdate()->firstOrFail();

                // Check stock if finishing immediately
                if ($statusPenjualan === 'selesai' && (float) $barang->stok_saat_ini < $jumlah) {
                    throw new InvalidArgumentException("Stok {$barang->nama_barang} tidak mencukupi (Tersedia: {$barang->stok_saat_ini}, Diminta: {$jumlah}).");
                }

                $hargaSatuan = isset($item['harga_satuan']) ? (float) $item['harga_satuan'] : (float) $barang->harga_jual;
                $hargaPokok = (float) $barang->harga_beli; // Base cost at time of sale
                $subtotal = $jumlah * $hargaSatuan;

                $totalHarga += $subtotal;
                $processedItems[] = [
                    'barang' => $barang,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'harga_pokok' => $hargaPokok,
                    'subtotal' => $subtotal,
                ];
            }

            $totalTagihan = max(0, $totalHarga - $diskon);

            // Cannot pay more than net bill
            $bayarAktual = min($jumlahBayarInput, $totalTagihan);

            // If credit sale (sisa_tagihan > 0), customer must be selected
            if ($totalTagihan > $bayarAktual) {
                if (!$pelangganId) {
                    throw new InvalidArgumentException('Pelanggan wajib dipilih untuk transaksi utang/kredit.');
                }
            }

            // Create Penjualan
            $penjualan = Penjualan::create([
                'pelanggan_id' => $pelangganId,
                'pengguna_id' => $userId,
                'nomor_faktur' => $nomorFaktur,
                'tanggal_penjualan' => $tanggalPenjualan,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'total_harga' => $totalHarga,
                'diskon' => $diskon,
                'total_bayar' => 0, // will be updated if payment recorded
                'status_penjualan' => $statusPenjualan,
                'catatan' => $catatan,
            ]);

            // Create Detail Penjualan and reduce stock if completed
            foreach ($processedItems as $pItem) {
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $pItem['barang_id'],
                    'jumlah' => $pItem['jumlah'],
                    'harga_satuan' => $pItem['harga_satuan'],
                    'harga_pokok' => $pItem['harga_pokok'],
                    'subtotal' => $pItem['subtotal'],
                ]);

                if ($statusPenjualan === 'selesai') {
                    $this->stockService->reduceStock(
                        $pItem['barang_id'],
                        $pItem['jumlah'],
                        'penjualan_keluar',
                        "Penjualan Faktur {$nomorFaktur}",
                        'penjualan',
                        $penjualan->id,
                        $userId
                    );
                }
            }

            // Record initial payment if any and sale is completed
            if ($statusPenjualan === 'selesai' && $bayarAktual > 0) {
                $validMetode = in_array($metodePembayaran, ['tunai', 'transfer', 'qris', 'lainnya']) ? $metodePembayaran : 'tunai';
                PembayaranPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'pengguna_id' => $userId,
                    'jumlah_bayar' => $bayarAktual,
                    'metode_pembayaran' => $validMetode,
                    'tanggal_pembayaran' => $tanggalPenjualan,
                    'catatan' => 'Pembayaran awal penjualan',
                ]);

                $penjualan->total_bayar = $bayarAktual;
                $penjualan->save();
            }

            ActivityLogService::log(
                'buat_penjualan',
                'penjualan',
                $penjualan->id,
                "Membuat penjualan faktur {$nomorFaktur} total Rp " . number_format($totalTagihan, 0, ',', '.') . " status: {$statusPenjualan}",
                $userId
            );

            return $penjualan;
        });
    }

    /**
     * Cancel a sales transaction and restore stock safely
     */
    public function cancelSale(int $penjualanId, string $alasan = 'Dibatalkan oleh petugas', ?int $penggunaId = null): Penjualan
    {
        return DB::transaction(function () use ($penjualanId, $alasan, $penggunaId) {
            $userId = $penggunaId ?? Auth::id();
            $penjualan = Penjualan::with('detailPenjualan')->where('id', $penjualanId)->lockForUpdate()->firstOrFail();

            if ($penjualan->status_penjualan === 'dibatalkan') {
                throw new InvalidArgumentException('Transaksi ini sudah dalam status dibatalkan.');
            }

            $statusSebelumnya = $penjualan->status_penjualan;

            // If it was 'selesai', restore stock
            if ($statusSebelumnya === 'selesai') {
                foreach ($penjualan->detailPenjualan as $detail) {
                    $this->stockService->addStock(
                        $detail->barang_id,
                        (float) $detail->jumlah,
                        'retur_masuk',
                        "Pembatalan penjualan faktur {$penjualan->nomor_faktur}: {$alasan}",
                        'pembatalan_penjualan',
                        $penjualan->id,
                        null,
                        $userId
                    );
                }
            }

            $penjualan->status_penjualan = 'dibatalkan';
            $penjualan->catatan = trim(($penjualan->catatan ?? '') . "\n[DIBATALKAN: {$alasan}]");
            $penjualan->save();

            ActivityLogService::log(
                'batal_penjualan',
                'penjualan',
                $penjualan->id,
                "Membatalkan penjualan faktur {$penjualan->nomor_faktur}. Alasan: {$alasan}",
                $userId
            );

            return $penjualan;
        });
    }
}
