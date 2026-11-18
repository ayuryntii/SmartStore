<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\DetailPenjualan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ForecastService
{
    /**
     * Forecast stock demand using Moving Average
     * 
     * @param int $barangId
     * @param int $historyDays Number of days of historical sales to analyze (default 30)
     * @param int $planningDays Number of days to plan for in future (default 7)
     * @return array
     */
    public function forecastItem(int $barangId, int $historyDays = 30, int $planningDays = 7): array
    {
        $barang = Barang::with(['satuan', 'kategori'])->findOrFail($barangId);

        $startDate = Carbon::now()->subDays($historyDays)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Calculate total sold in period
        $totalTerjual = (float) DetailPenjualan::where('barang_id', $barangId)
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate) {
                $q->where('status_penjualan', 'selesai')
                    ->whereBetween('tanggal_penjualan', [$startDate, $endDate]);
            })
            ->sum('jumlah');

        $rataRataHarian = $historyDays > 0 ? ($totalTerjual / $historyDays) : 0;
        $stokSaatIni = (float) $barang->stok_saat_ini;
        $stokMinimum = (float) $barang->stok_minimum;

        // Kebutuhan Periode = Rata-rata Penjualan Harian × Jumlah Hari Perencanaan
        $kebutuhanPeriode = $rataRataHarian * $planningDays;

        // Rekomendasi Pembelian = Maksimum (0, Kebutuhan Periode + Target Stok Aman - Stok Saat Ini)
        $rekomendasiPembelian = max(0, ceil($kebutuhanPeriode + $stokMinimum - $stokSaatIni));

        $hasEnoughData = $totalTerjual > 0;

        return [
            'barang_id' => $barang->id,
            'nama_barang' => $barang->nama_barang,
            'kode_barang' => $barang->kode_barang,
            'kategori' => $barang->kategori?->nama_kategori ?? '-',
            'satuan' => $barang->satuan?->singkatan ?? 'pcs',
            'harga_beli' => (float) $barang->harga_beli,
            'stok_saat_ini' => $stokSaatIni,
            'stok_minimum' => $stokMinimum,
            'history_days' => $historyDays,
            'planning_days' => $planningDays,
            'total_terjual' => $totalTerjual,
            'rata_rata_harian' => round($rataRataHarian, 2),
            'kebutuhan_periode' => round($kebutuhanPeriode, 2),
            'rekomendasi_pembelian' => $rekomendasiPembelian,
            'estimasi_biaya' => $rekomendasiPembelian * (float) $barang->harga_beli,
            'has_enough_data' => $hasEnoughData,
            'catatan' => $hasEnoughData
                ? "Dihitung dari riwayat {$historyDays} hari terakhir."
                : "Belum ada riwayat penjualan signifikan dalam {$historyDays} hari terakhir.",
        ];
    }

    /**
     * Forecast all active items or filtered by category/status
     */
    public function forecastAll(int $historyDays = 30, int $planningDays = 7, ?int $kategoriId = null): Collection
    {
        $query = Barang::where('status_aktif', 1);

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        $items = $query->orderBy('nama_barang')->get();

        return $items->map(function ($barang) use ($historyDays, $planningDays) {
            return $this->forecastItem($barang->id, $historyDays, $planningDays);
        });
    }
}
