<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\DetailRencanaBelanja;
use App\Models\RencanaBelanja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShoppingPlanService
{
    public function __construct(
        protected ForecastService $forecastService
    ) {}

    /**
     * Auto generate recommendations for a shopping plan
     */
    public function generateRecommendations(): array
    {
        $recommendations = [];

        // 1. Out of stock items
        $outOfStock = Barang::where('status_aktif', 1)
            ->where('stok_saat_ini', '<=', 0)
            ->with(['satuan', 'kategori'])
            ->get();

        foreach ($outOfStock as $b) {
            $forecast = $this->forecastService->forecastItem($b->id, 30, 7);
            $qty = $forecast['rekomendasi_pembelian'] > 0
                ? $forecast['rekomendasi_pembelian']
                : max(10, ((float) $b->stok_minimum > 0 ? (float) $b->stok_minimum * 2 : 10));

            $recommendations[$b->id] = [
                'barang_id' => $b->id,
                'nama_barang' => $b->nama_barang,
                'kode_barang' => $b->kode_barang,
                'kategori' => $b->kategori?->nama_kategori ?? '-',
                'satuan' => $b->satuan?->singkatan ?? 'pcs',
                'stok_saat_ini' => (float) $b->stok_saat_ini,
                'stok_minimum' => (float) $b->stok_minimum,
                'harga_beli' => (float) $b->harga_beli,
                'jumlah_rekomendasi' => $qty,
                'alasan' => 'Stok Habis (0)',
                'urgensi' => 'Tinggi',
            ];
        }

        // 2. Low stock items
        $lowStock = Barang::where('status_aktif', 1)
            ->where('stok_saat_ini', '>', 0)
            ->whereColumn('stok_saat_ini', '<=', 'stok_minimum')
            ->with(['satuan', 'kategori'])
            ->get();

        foreach ($lowStock as $b) {
            if (isset($recommendations[$b->id])) {
                continue;
            }

            $forecast = $this->forecastService->forecastItem($b->id, 30, 7);
            $qty = $forecast['rekomendasi_pembelian'] > 0
                ? $forecast['rekomendasi_pembelian']
                : max(5, ((float) $b->stok_minimum * 2) - (float) $b->stok_saat_ini);

            $recommendations[$b->id] = [
                'barang_id' => $b->id,
                'nama_barang' => $b->nama_barang,
                'kode_barang' => $b->kode_barang,
                'kategori' => $b->kategori?->nama_kategori ?? '-',
                'satuan' => $b->satuan?->singkatan ?? 'pcs',
                'stok_saat_ini' => (float) $b->stok_saat_ini,
                'stok_minimum' => (float) $b->stok_minimum,
                'harga_beli' => (float) $b->harga_beli,
                'jumlah_rekomendasi' => $qty,
                'alasan' => 'Stok Menipis (<= minimum)',
                'urgensi' => 'Sedang',
            ];
        }

        return array_values($recommendations);
    }

    /**
     * Create shopping plan with items
     */
    public function createPlan(array $data, ?int $penggunaId = null): RencanaBelanja
    {
        return DB::transaction(function () use ($data, $penggunaId) {
            $userId = $penggunaId ?? Auth::id();

            if (empty($data['items']) || !is_array($data['items'])) {
                throw new InvalidArgumentException('Rencana belanja harus memiliki minimal satu barang.');
            }

            $plan = RencanaBelanja::create([
                'pengguna_id' => $userId,
                'tanggal_rencana' => $data['tanggal_rencana'] ?? now()->toDateString(),
                'status_rencana' => $data['status_rencana'] ?? 'draf',
                'catatan' => $data['catatan'] ?? null,
            ]);

            $seenBarangIds = [];
            foreach ($data['items'] as $item) {
                $barangId = (int) $item['barang_id'];
                $jumlahDibutuhkan = (float) $item['jumlah_dibutuhkan'];

                if (in_array($barangId, $seenBarangIds)) {
                    // Avoid duplicate item in same plan
                    continue;
                }
                $seenBarangIds[] = $barangId;

                if ($jumlahDibutuhkan <= 0) {
                    continue;
                }

                DetailRencanaBelanja::create([
                    'rencana_belanja_id' => $plan->id,
                    'barang_id' => $barangId,
                    'jumlah_dibutuhkan' => $jumlahDibutuhkan,
                    'jumlah_dibeli' => (float) ($item['jumlah_dibeli'] ?? 0),
                    'status_item' => $item['status_item'] ?? 'menunggu',
                ]);
            }

            ActivityLogService::log(
                'buat_rencana_belanja',
                'rencana_belanja',
                $plan->id,
                "Membuat rencana belanja ID #{$plan->id} tanggal {$plan->tanggal_rencana}",
                $userId
            );

            return $plan;
        });
    }

    /**
     * Update detail item in shopping plan
     */
    public function updateItemStatus(int $detailId, string $statusItem, float $jumlahDibeli = 0): DetailRencanaBelanja
    {
        $allowed = ['menunggu', 'dibeli', 'dibatalkan'];
        if (!in_array($statusItem, $allowed)) {
            throw new InvalidArgumentException("Status item tidak valid: {$statusItem}");
        }

        $detail = DetailRencanaBelanja::with('rencanaBelanja')->findOrFail($detailId);
        $detail->status_item = $statusItem;
        if ($statusItem === 'dibeli') {
            $detail->jumlah_dibeli = $jumlahDibeli > 0 ? $jumlahDibeli : $detail->jumlah_dibutuhkan;
        }
        $detail->save();

        // Check if all items in plan are resolved
        $plan = $detail->rencanaBelanja;
        $unresolved = DetailRencanaBelanja::where('rencana_belanja_id', $plan->id)
            ->where('status_item', 'menunggu')
            ->count();

        if ($unresolved === 0 && $plan->status_rencana !== 'selesai' && $plan->status_rencana !== 'dibatalkan') {
            $plan->status_rencana = 'selesai';
            $plan->save();
        }

        return $detail;
    }
}
