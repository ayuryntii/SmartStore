<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BatchBarang;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Generate purchase invoice number: PB-YYYYMMDD-XXXX
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'PB-' . date('Ymd') . '-';
        $last = Pembelian::where('nomor_faktur', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->nomor_faktur, strlen($prefix));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    /**
     * Create purchase transaction
     */
    public function createPurchase(array $data, ?int $penggunaId = null): Pembelian
    {
        return DB::transaction(function () use ($data, $penggunaId) {
            $userId = $penggunaId ?? Auth::id();

            if (empty($data['pemasok_id'])) {
                throw new InvalidArgumentException('Pemasok wajib dipilih.');
            }

            if (empty($data['items']) || !is_array($data['items'])) {
                throw new InvalidArgumentException('Pembelian harus memiliki minimal satu barang.');
            }

            $nomorFaktur = !empty($data['nomor_faktur']) ? $data['nomor_faktur'] : $this->generateInvoiceNumber();
            $tanggalPembelian = $data['tanggal_pembelian'] ?? now();
            $statusPembelian = $data['status_pembelian'] ?? 'draf';
            $catatan = $data['catatan'] ?? null;

            $totalPembelian = 0;
            $itemsToProcess = [];

            foreach ($data['items'] as $item) {
                $barangId = (int) $item['barang_id'];
                $jumlah = (float) $item['jumlah'];
                $hargaSatuan = (float) $item['harga_satuan'];

                if ($jumlah <= 0) {
                    throw new InvalidArgumentException('Jumlah barang harus lebih dari 0.');
                }
                if ($hargaSatuan < 0) {
                    throw new InvalidArgumentException('Harga beli tidak boleh negatif.');
                }

                $barang = Barang::findOrFail($barangId);
                $subtotal = $jumlah * $hargaSatuan;
                $totalPembelian += $subtotal;

                $itemsToProcess[] = [
                    'barang' => $barang,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                    'kode_batch' => $item['kode_batch'] ?? null,
                    'tanggal_kedaluwarsa' => !empty($item['tanggal_kedaluwarsa']) ? $item['tanggal_kedaluwarsa'] : null,
                ];
            }

            $pembelian = Pembelian::create([
                'pemasok_id' => $data['pemasok_id'],
                'pengguna_id' => $userId,
                'nomor_faktur' => $nomorFaktur,
                'tanggal_pembelian' => $tanggalPembelian,
                'status_pembelian' => $statusPembelian,
                'total_pembelian' => $totalPembelian,
                'catatan' => $catatan,
            ]);

            foreach ($itemsToProcess as $pItem) {
                $detail = DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $pItem['barang_id'],
                    'jumlah' => $pItem['jumlah'],
                    'harga_satuan' => $pItem['harga_satuan'],
                    'subtotal' => $pItem['subtotal'],
                ]);

                if ($statusPembelian === 'diterima') {
                    $batchId = null;
                    if (!empty($pItem['kode_batch']) || !empty($pItem['tanggal_kedaluwarsa'])) {
                        $batch = BatchBarang::create([
                            'detail_pembelian_id' => $detail->id,
                            'barang_id' => $pItem['barang_id'],
                            'kode_batch' => $pItem['kode_batch'],
                            'tanggal_kedaluwarsa' => $pItem['tanggal_kedaluwarsa'],
                            'jumlah_diterima' => $pItem['jumlah'],
                            'sisa_jumlah' => $pItem['jumlah'],
                        ]);
                        $batchId = $batch->id;
                    }

                    // Add stock
                    $this->stockService->addStock(
                        $pItem['barang_id'],
                        $pItem['jumlah'],
                        'pembelian_masuk',
                        "Pembelian Faktur {$nomorFaktur}",
                        'pembelian',
                        $pembelian->id,
                        $batchId,
                        $userId
                    );

                    // Update item latest purchase price if changed
                    $pItem['barang']->update(['harga_beli' => $pItem['harga_satuan']]);
                }
            }

            ActivityLogService::log(
                'buat_pembelian',
                'pembelian',
                $pembelian->id,
                "Mencatat pembelian faktur {$nomorFaktur} status: {$statusPembelian} total: Rp " . number_format($totalPembelian, 0, ',', '.'),
                $userId
            );

            return $pembelian;
        });
    }

    /**
     * Mark a draft purchase as received (diterima) and increase stock
     * Safeguards against double increment
     */
    public function receivePurchase(int $pembelianId, ?int $penggunaId = null): Pembelian
    {
        return DB::transaction(function () use ($pembelianId, $penggunaId) {
            $userId = $penggunaId ?? Auth::id();
            $pembelian = Pembelian::with('detailPembelian.barang')->where('id', $pembelianId)->lockForUpdate()->firstOrFail();

            if ($pembelian->status_pembelian === 'diterima') {
                throw new InvalidArgumentException("Pembelian faktur {$pembelian->nomor_faktur} sudah berstatus diterima sebelumnya.");
            }

            if ($pembelian->status_pembelian === 'dibatalkan') {
                throw new InvalidArgumentException('Pembelian yang sudah dibatalkan tidak dapat diterima secara langsung.');
            }

            foreach ($pembelian->detailPembelian as $detail) {
                // Check if batch already exists for this detail
                $existingBatch = BatchBarang::where('detail_pembelian_id', $detail->id)->first();
                $batchId = $existingBatch?->id;

                $this->stockService->addStock(
                    $detail->barang_id,
                    (float) $detail->jumlah,
                    'pembelian_masuk',
                    "Penerimaan Pembelian Faktur {$pembelian->nomor_faktur}",
                    'pembelian',
                    $pembelian->id,
                    $batchId,
                    $userId
                );

                // Update latest buying price
                $detail->barang->update(['harga_beli' => $detail->harga_satuan]);
            }

            $pembelian->status_pembelian = 'diterima';
            $pembelian->save();

            ActivityLogService::log(
                'terima_pembelian',
                'pembelian',
                $pembelian->id,
                "Menerima barang untuk pembelian faktur {$pembelian->nomor_faktur}",
                $userId
            );

            return $pembelian;
        });
    }
}
