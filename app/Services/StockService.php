<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BatchBarang;
use App\Models\DetailRencanaBelanja;
use App\Models\RencanaBelanja;
use App\Models\RiwayatStok;
use App\Models\RiwayatStokHabis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    /**
     * Set initial stock for a newly created or initialized item
     */
    public function setInitialStock(Barang $barang, float $stokAwal, ?int $penggunaId = null): void
    {
        if ($stokAwal <= 0) {
            $barang->update(['stok_saat_ini' => 0]);
            $this->checkOutOfStockEvent($barang->id, 0, 0, 'Stok awal 0 saat pendaftaran barang');
            return;
        }

        DB::transaction(function () use ($barang, $stokAwal, $penggunaId) {
            $userId = $penggunaId ?? Auth::id();

            $barang->stok_saat_ini = $stokAwal;
            $barang->save();

            RiwayatStok::create([
                'barang_id' => $barang->id,
                'batch_id' => null,
                'pengguna_id' => $userId,
                'jenis_pergerakan' => 'penyesuaian_masuk',
                'jumlah' => $stokAwal,
                'jenis_referensi' => 'stok_awal',
                'referensi_id' => $barang->id,
                'catatan' => 'Pencatatan stok awal barang',
                'tanggal_pergerakan' => now(),
            ]);

            ActivityLogService::log(
                'stok_awal',
                'barang',
                $barang->id,
                "Mencatat stok awal barang {$barang->nama_barang} sebanyak {$stokAwal} {$barang->satuan?->singkatan}",
                $userId
            );
        });
    }

    /**
     * Add stock manually or through adjustment / return
     */
    public function addStock(
        int $barangId,
        float $jumlah,
        string $jenisPergerakan,
        ?string $catatan = null,
        ?string $jenisReferensi = null,
        ?int $referensiId = null,
        ?int $batchId = null,
        ?int $penggunaId = null
    ): void {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException('Jumlah penambahan stok harus lebih besar dari nol.');
        }

        $allowed = ['pembelian_masuk', 'penyesuaian_masuk', 'retur_masuk'];
        if (!in_array($jenisPergerakan, $allowed)) {
            throw new InvalidArgumentException("Jenis pergerakan penambahan tidak valid: {$jenisPergerakan}");
        }

        DB::transaction(function () use (
            $barangId,
            $jumlah,
            $jenisPergerakan,
            $catatan,
            $jenisReferensi,
            $referensiId,
            $batchId,
            $penggunaId
        ) {
            $userId = $penggunaId ?? Auth::id();
            $barang = Barang::where('id', $barangId)->lockForUpdate()->firstOrFail();

            $stokSebelumnya = (float) $barang->stok_saat_ini;
            $stokBaru = $stokSebelumnya + $jumlah;

            $barang->stok_saat_ini = $stokBaru;
            $barang->save();

            // Record history
            RiwayatStok::create([
                'barang_id' => $barang->id,
                'batch_id' => $batchId,
                'pengguna_id' => $userId,
                'jenis_pergerakan' => $jenisPergerakan,
                'jumlah' => $jumlah,
                'jenis_referensi' => $jenisReferensi,
                'referensi_id' => $referensiId,
                'catatan' => $catatan,
                'tanggal_pergerakan' => now(),
            ]);

            // If item was out of stock, close out of stock event
            $this->closeOutOfStockEventIfExists($barang->id);

            ActivityLogService::log(
                $jenisPergerakan,
                'barang',
                $barang->id,
                "Penambahan stok {$barang->nama_barang}: +{$jumlah} (stok baru: {$stokBaru})",
                $userId
            );
        });
    }

    /**
     * Deduct stock using FEFO (First Expired First Out) when batches exist
     */
    public function reduceStock(
        int $barangId,
        float $jumlah,
        string $jenisPergerakan,
        ?string $catatan = null,
        ?string $jenisReferensi = null,
        ?int $referensiId = null,
        ?int $penggunaId = null
    ): void {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException('Jumlah pengurangan stok harus lebih besar dari nol.');
        }

        $allowed = ['penjualan_keluar', 'penyesuaian_keluar', 'retur_keluar'];
        if (!in_array($jenisPergerakan, $allowed)) {
            throw new InvalidArgumentException("Jenis pergerakan pengurangan tidak valid: {$jenisPergerakan}");
        }

        DB::transaction(function () use (
            $barangId,
            $jumlah,
            $jenisPergerakan,
            $catatan,
            $jenisReferensi,
            $referensiId,
            $penggunaId
        ) {
            $userId = $penggunaId ?? Auth::id();
            $barang = Barang::where('id', $barangId)->lockForUpdate()->firstOrFail();

            $stokSebelumnya = (float) $barang->stok_saat_ini;
            if ($stokSebelumnya < $jumlah) {
                throw new InvalidArgumentException("Stok barang {$barang->nama_barang} tidak mencukupi (Tersedia: {$stokSebelumnya}, Dibutuhkan: {$jumlah}).");
            }

            $stokBaru = $stokSebelumnya - $jumlah;
            $barang->stok_saat_ini = $stokBaru;
            $barang->save();

            // FEFO batch allocation: find active batches sorted by expiry date ascending
            $sisaKebutuhan = $jumlah;
            $batches = BatchBarang::where('barang_id', $barangId)
                ->where('sisa_jumlah', '>', 0)
                ->orderByRaw('CASE WHEN tanggal_kedaluwarsa IS NULL THEN 1 ELSE 0 END, tanggal_kedaluwarsa ASC, id ASC')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($sisaKebutuhan <= 0) {
                    break;
                }

                $sisaBatch = (float) $batch->sisa_jumlah;
                $ambil = min($sisaBatch, $sisaKebutuhan);

                $batch->sisa_jumlah = $sisaBatch - $ambil;
                $batch->save();

                $sisaKebutuhan -= $ambil;

                RiwayatStok::create([
                    'barang_id' => $barang->id,
                    'batch_id' => $batch->id,
                    'pengguna_id' => $userId,
                    'jenis_pergerakan' => $jenisPergerakan,
                    'jumlah' => $ambil,
                    'jenis_referensi' => $jenisReferensi,
                    'referensi_id' => $referensiId,
                    'catatan' => $catatan . ($batch->kode_batch ? " (Batch: {$batch->kode_batch})" : ''),
                    'tanggal_pergerakan' => now(),
                ]);
            }

            // If remaining quantity without batch (e.g. stock added before batch system or manual stock)
            if ($sisaKebutuhan > 0) {
                RiwayatStok::create([
                    'barang_id' => $barang->id,
                    'batch_id' => null,
                    'pengguna_id' => $userId,
                    'jenis_pergerakan' => $jenisPergerakan,
                    'jumlah' => $sisaKebutuhan,
                    'jenis_referensi' => $jenisReferensi,
                    'referensi_id' => $referensiId,
                    'catatan' => $catatan,
                    'tanggal_pergerakan' => now(),
                ]);
            }

            // Check if stock became 0
            $this->checkOutOfStockEvent($barang->id, $stokSebelumnya, $stokBaru, $catatan);

            ActivityLogService::log(
                $jenisPergerakan,
                'barang',
                $barang->id,
                "Pengurangan stok {$barang->nama_barang}: -{$jumlah} (stok tersisa: {$stokBaru})",
                $userId
            );
        });
    }

    /**
     * Feature: "Tombol Laporkan Barang Habis"
     * As per section 5.E:
     * - Confirm out-of-stock
     * - Show current stock
     * - Deduct remaining stock using penyesuaian_keluar if stock > 0
     * - If stock is already 0, do not deduct twice
     * - Record out-of-stock event only if not already open
     * - Put item into shopping recommendation
     */
    public function reportOutOfStock(int $barangId, string $alasan, ?int $penggunaId = null): array
    {
        return DB::transaction(function () use ($barangId, $alasan, $penggunaId) {
            $userId = $penggunaId ?? Auth::id();
            $barang = Barang::where('id', $barangId)->lockForUpdate()->firstOrFail();

            $stokSebelumnya = (float) $barang->stok_saat_ini;
            $sudahNol = ($stokSebelumnya <= 0);

            if (!$sudahNol) {
                // Deduct remaining stock to zero
                $barang->stok_saat_ini = 0;
                $barang->save();

                // Clear any remaining batch numbers
                BatchBarang::where('barang_id', $barangId)
                    ->where('sisa_jumlah', '>', 0)
                    ->update(['sisa_jumlah' => 0]);

                RiwayatStok::create([
                    'barang_id' => $barang->id,
                    'batch_id' => null,
                    'pengguna_id' => $userId,
                    'jenis_pergerakan' => 'penyesuaian_keluar',
                    'jumlah' => $stokSebelumnya,
                    'jenis_referensi' => 'laporan_habis',
                    'referensi_id' => $barang->id,
                    'catatan' => 'Laporan fisik barang habis: ' . $alasan,
                    'tanggal_pergerakan' => now(),
                ]);
            }

            // Create out of stock event if not open
            $openEvent = RiwayatStokHabis::where('barang_id', $barangId)
                ->whereNull('berakhir_pada')
                ->first();

            if (!$openEvent) {
                RiwayatStokHabis::create([
                    'barang_id' => $barangId,
                    'mulai_habis_pada' => now(),
                    'catatan' => 'Laporan habis: ' . $alasan,
                ]);
            }

            // Put item into shopping recommendation (active draft plan or prepare plan)
            $this->addOrUpdateShoppingPlanRecommendation($barangId, $userId);

            ActivityLogService::log(
                'lapor_habis',
                'barang',
                $barangId,
                "Melaporkan barang {$barang->nama_barang} habis fisik (stok sistem sebelumnya: {$stokSebelumnya}). Alasan: {$alasan}",
                $userId
            );

            return [
                'success' => true,
                'stok_sebelumnya' => $stokSebelumnya,
                'pesan' => "Barang {$barang->nama_barang} berhasil dilaporkan habis dan dimasukkan ke rekomendasi belanja.",
            ];
        });
    }

    /**
     * Check if stock reached 0 and create out-of-stock event if needed
     */
    public function checkOutOfStockEvent(int $barangId, float $stokSebelumnya, float $stokBaru, ?string $catatan = null): void
    {
        if ($stokBaru <= 0 && $stokSebelumnya > 0) {
            $openEvent = RiwayatStokHabis::where('barang_id', $barangId)
                ->whereNull('berakhir_pada')
                ->first();

            if (!$openEvent) {
                RiwayatStokHabis::create([
                    'barang_id' => $barangId,
                    'mulai_habis_pada' => now(),
                    'catatan' => $catatan ?? 'Stok mencapai 0',
                ]);
            }
        }
    }

    /**
     * Close any open out-of-stock event when stock becomes available
     */
    public function closeOutOfStockEventIfExists(int $barangId): void
    {
        RiwayatStokHabis::where('barang_id', $barangId)
            ->whereNull('berakhir_pada')
            ->update(['berakhir_pada' => now()]);
    }

    /**
     * Add item into active shopping plan recommendation
     */
    protected function addOrUpdateShoppingPlanRecommendation(int $barangId, int $userId): void
    {
        $plan = RencanaBelanja::where('status_rencana', 'draf')
            ->orderBy('id', 'desc')
            ->first();

        if (!$plan) {
            $plan = RencanaBelanja::create([
                'pengguna_id' => $userId,
                'tanggal_rencana' => now()->toDateString(),
                'status_rencana' => 'draf',
                'catatan' => 'Rencana belanja otomatis dari deteksi barang habis / stok menipis',
            ]);
        }

        $barang = Barang::find($barangId);
        $minStock = $barang ? (float) $barang->stok_minimum : 10;
        $kebutuhan = max(10, $minStock > 0 ? $minStock * 2 : 10);

        $detail = DetailRencanaBelanja::where('rencana_belanja_id', $plan->id)
            ->where('barang_id', $barangId)
            ->first();

        if (!$detail) {
            DetailRencanaBelanja::create([
                'rencana_belanja_id' => $plan->id,
                'barang_id' => $barangId,
                'jumlah_dibutuhkan' => $kebutuhan,
                'jumlah_dibeli' => 0,
                'status_item' => 'menunggu',
            ]);
        }
    }
}
