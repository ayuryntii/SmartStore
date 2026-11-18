<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BatchBarang;
use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\PembayaranPenjualan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\RencanaBelanja;
use App\Models\RiwayatStok;
use App\Models\RiwayatStokHabis;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * 1. Laporan Data Barang
     */
    public function getBarangReport(?int $kategoriId = null, ?int $statusAktif = null)
    {
        $query = Barang::with(['kategori', 'satuan'])->orderBy('nama_barang');

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }
        if (!is_null($statusAktif)) {
            $query->where('status_aktif', $statusAktif);
        }

        return $query->get();
    }

    /**
     * 2, 3, 4. Laporan Stok (Semua / Minimum / Habis)
     */
    public function getStockReport(string $filter = 'semua', ?int $kategoriId = null)
    {
        $query = Barang::with(['kategori', 'satuan'])->where('status_aktif', 1)->orderBy('nama_barang');

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($filter === 'habis') {
            $query->where('stok_saat_ini', '<=', 0);
        } elseif ($filter === 'menipis') {
            $query->where('stok_saat_ini', '>', 0)
                ->whereColumn('stok_saat_ini', '<=', 'stok_minimum');
        } elseif ($filter === 'aman') {
            $query->whereColumn('stok_saat_ini', '>', 'stok_minimum');
        }

        return $query->get();
    }

    /**
     * 5 & 6. Laporan Pergerakan Barang (Masuk / Keluar)
     */
    public function getStockMovementReport(
        string $arah = 'semua', // 'masuk', 'keluar', 'semua'
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $barangId = null
    ) {
        $query = RiwayatStok::with(['barang.satuan', 'pengguna', 'batch'])
            ->orderBy('tanggal_pergerakan', 'desc');

        if ($arah === 'masuk') {
            $query->whereIn('jenis_pergerakan', ['pembelian_masuk', 'penyesuaian_masuk', 'retur_masuk']);
        } elseif ($arah === 'keluar') {
            $query->whereIn('jenis_pergerakan', ['penjualan_keluar', 'penyesuaian_keluar', 'retur_keluar']);
        }

        if ($startDate) {
            $query->whereDate('tanggal_pergerakan', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_pergerakan', '<=', $endDate);
        }
        if ($barangId) {
            $query->where('barang_id', $barangId);
        }

        return $query->get();
    }

    /**
     * 7. Laporan Penjualan
     */
    public function getSalesReport(
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $pelangganId = null,
        ?string $statusPenjualan = null
    ) {
        $query = Penjualan::with(['pelanggan', 'pengguna', 'detailPenjualan.barang.satuan'])
            ->orderBy('tanggal_penjualan', 'desc');

        if ($startDate) {
            $query->whereDate('tanggal_penjualan', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_penjualan', '<=', $endDate);
        }
        if ($pelangganId) {
            $query->where('pelanggan_id', $pelangganId);
        }
        if ($statusPenjualan) {
            $query->where('status_penjualan', $statusPenjualan);
        }

        return $query->get();
    }

    /**
     * 8. Laporan Pembelian
     */
    public function getPurchaseReport(
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $pemasokId = null,
        ?string $statusPembelian = null
    ) {
        $query = Pembelian::with(['pemasok', 'pengguna', 'detailPembelian.barang.satuan'])
            ->orderBy('tanggal_pembelian', 'desc');

        if ($startDate) {
            $query->whereDate('tanggal_pembelian', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_pembelian', '<=', $endDate);
        }
        if ($pemasokId) {
            $query->where('pemasok_id', $pemasokId);
        }
        if ($statusPembelian) {
            $query->where('status_pembelian', $statusPembelian);
        }

        return $query->get();
    }

    /**
     * 10. Laporan Piutang Pelanggan
     */
    public function getDebtReport(?int $pelangganId = null, ?string $statusPembayaran = null)
    {
        $query = Penjualan::with(['pelanggan', 'pembayaranPenjualan'])
            ->where('status_penjualan', 'selesai')
            ->orderBy('tanggal_penjualan', 'desc');

        if ($pelangganId) {
            $query->where('pelanggan_id', $pelangganId);
        }

        $sales = $query->get();

        if ($statusPembayaran) {
            $sales = $sales->filter(function ($s) use ($statusPembayaran) {
                return $s->status_pembayaran === $statusPembayaran;
            });
        }

        return $sales;
    }

    /**
     * 11. Laporan Pembayaran Utang
     */
    public function getDebtPaymentsReport(?string $startDate = null, ?string $endDate = null, ?int $pelangganId = null)
    {
        $query = PembayaranPenjualan::with(['penjualan.pelanggan', 'pengguna'])
            ->orderBy('tanggal_pembayaran', 'desc');

        if ($startDate) {
            $query->whereDate('tanggal_pembayaran', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_pembayaran', '<=', $endDate);
        }
        if ($pelangganId) {
            $query->whereHas('penjualan', function ($q) use ($pelangganId) {
                $q->where('pelanggan_id', $pelangganId);
            });
        }

        return $query->get();
    }

    /**
     * 12. Laporan Barang Terlaris & Kurang Laku
     */
    public function getTopAndSlowProductsReport(?string $startDate = null, ?string $endDate = null)
    {
        $query = DetailPenjualan::select(
            'barang_id',
            DB::raw('SUM(detail_penjualan.jumlah) as total_terjual'),
            DB::raw('SUM(detail_penjualan.subtotal) as total_pendapatan'),
            DB::raw('SUM(detail_penjualan.jumlah * detail_penjualan.harga_pokok) as total_hpp'),
            DB::raw('SUM(detail_penjualan.subtotal - (detail_penjualan.jumlah * detail_penjualan.harga_pokok)) as total_laba_kotor')
        )
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->where('penjualan.status_penjualan', 'selesai');

        if ($startDate) {
            $query->whereDate('penjualan.tanggal_penjualan', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('penjualan.tanggal_penjualan', '<=', $endDate);
        }

        $salesGrouped = $query->groupBy('barang_id')->get()->keyBy('barang_id');

        $allBarang = Barang::with(['kategori', 'satuan'])->where('status_aktif', 1)->get();

        $result = $allBarang->map(function ($b) use ($salesGrouped) {
            $stat = $salesGrouped->get($b->id);
            return [
                'barang' => $b,
                'total_terjual' => $stat ? (float) $stat->total_terjual : 0,
                'total_pendapatan' => $stat ? (float) $stat->total_pendapatan : 0,
                'total_hpp' => $stat ? (float) $stat->total_hpp : 0,
                'total_laba_kotor' => $stat ? (float) $stat->total_laba_kotor : 0,
            ];
        });

        return [
            'terlaris' => $result->sortByDesc('total_terjual')->values(),
            'kurang_laku' => $result->sortBy('total_terjual')->values(),
        ];
    }

    /**
     * 13. Laporan Barang Paling Sering Habis
     */
    public function getFrequentlyOutOfStockReport(?string $startDate = null, ?string $endDate = null)
    {
        $query = RiwayatStokHabis::select(
            'barang_id',
            DB::raw('COUNT(id) as frekuensi_habis'),
            DB::raw('MAX(mulai_habis_pada) as terakhir_habis')
        );

        if ($startDate) {
            $query->whereDate('mulai_habis_pada', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('mulai_habis_pada', '<=', $endDate);
        }

        $stats = $query->groupBy('barang_id')->orderByDesc('frekuensi_habis')->get();

        return $stats->map(function ($s) {
            $barang = Barang::with(['kategori', 'satuan'])->find($s->barang_id);
            // Calculate average duration in hours
            $events = RiwayatStokHabis::where('barang_id', $s->barang_id)->get();
            $totalHours = 0;
            $counted = 0;
            foreach ($events as $e) {
                if ($e->berakhir_pada) {
                    $totalHours += $e->mulai_habis_pada->diffInHours($e->berakhir_pada);
                    $counted++;
                }
            }
            $avgDuration = $counted > 0 ? round($totalHours / $counted, 1) . ' Jam' : 'Masih Habis';

            return [
                'barang' => $barang,
                'frekuensi_habis' => $s->frekuensi_habis,
                'terakhir_habis' => $s->terakhir_habis,
                'rata_rata_durasi' => $avgDuration,
                'stok_saat_ini' => $barang ? (float) $barang->stok_saat_ini : 0,
            ];
        });
    }

    /**
     * 14. Laporan Kedaluwarsa
     */
    public function getExpiryReport(string $filter = 'semua')
    {
        $query = BatchBarang::with(['barang.satuan', 'detailPembelian.pembelian.pemasok'])
            ->where('sisa_jumlah', '>', 0)
            ->whereNotNull('tanggal_kedaluwarsa')
            ->orderBy('tanggal_kedaluwarsa', 'asc');

        $now = now()->startOfDay();

        if ($filter === 'sudah_kedaluwarsa') {
            $query->whereDate('tanggal_kedaluwarsa', '<', $now);
        } elseif ($filter === '7_hari') {
            $query->whereBetween('tanggal_kedaluwarsa', [$now, $now->copy()->addDays(7)]);
        } elseif ($filter === '30_hari') {
            $query->whereBetween('tanggal_kedaluwarsa', [$now, $now->copy()->addDays(30)]);
        }

        return $query->get();
    }

    /**
     * 15. Laporan Keuangan Sederhana
     */
    public function getFinancialReport(?string $startDate = null, ?string $endDate = null): array
    {
        $salesQuery = Penjualan::where('status_penjualan', 'selesai');
        $purchaseQuery = Pembelian::where('status_pembelian', 'diterima');
        $paymentQuery = PembayaranPenjualan::query();

        if ($startDate) {
            $salesQuery->whereDate('tanggal_penjualan', '>=', $startDate);
            $purchaseQuery->whereDate('tanggal_pembelian', '>=', $startDate);
            $paymentQuery->whereDate('tanggal_pembayaran', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->whereDate('tanggal_penjualan', '<=', $endDate);
            $purchaseQuery->whereDate('tanggal_pembelian', '<=', $endDate);
            $paymentQuery->whereDate('tanggal_pembayaran', '<=', $endDate);
        }

        $sales = $salesQuery->get();
        $totalPenjualanKotor = (float) $sales->sum('total_harga');
        $totalDiskon = (float) $sales->sum('diskon');
        $totalPenjualanBersih = max(0, $totalPenjualanKotor - $totalDiskon);

        $totalPembelian = (float) $purchaseQuery->sum('total_pembelian');

        // Total payments actually received in cash/transfer/QRIS
        $totalKasMasuk = (float) $paymentQuery->sum('jumlah_bayar');

        // Unpaid receivables from completed sales
        $totalPiutangBelumDibayar = (float) $sales->sum(function ($s) {
            return $s->sisa_tagihan;
        });

        // Calculate gross profit from completed sales in the period
        $saleIds = $sales->pluck('id');
        $totalHpp = (float) DetailPenjualan::whereIn('penjualan_id', $saleIds)
            ->sum(DB::raw('jumlah * harga_pokok'));
        $estimasiLabaKotor = max(0, $totalPenjualanBersih - $totalHpp);

        // Arus Kas Bersih Operasional = Kas Masuk - Pembelian
        $arusKasBersih = $totalKasMasuk - $totalPembelian;

        return [
            'total_penjualan_kotor' => $totalPenjualanKotor,
            'total_diskon' => $totalDiskon,
            'total_penjualan_bersih' => $totalPenjualanBersih,
            'total_kas_masuk' => $totalKasMasuk,
            'total_pembelian' => $totalPembelian,
            'total_piutang_belum_dibayar' => $totalPiutangBelumDibayar,
            'total_hpp' => $totalHpp,
            'estimasi_laba_kotor' => $estimasiLabaKotor,
            'arus_kas_bersih' => $arusKasBersih,
            'margin_laba_persen' => $totalPenjualanBersih > 0 ? round(($estimasiLabaKotor / $totalPenjualanBersih) * 100, 1) : 0,
        ];
    }

    /**
     * Export generic table data to CSV/Excel format
     */
    public function exportCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
