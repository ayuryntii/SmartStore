<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\RencanaBelanja;
use App\Models\RiwayatAktivitas;
use App\Models\RiwayatStok;
use App\Models\RiwayatStokHabis;
use App\Services\ForecastService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected ForecastService $forecastService
    ) {}

    public function index()
    {
        $today = Carbon::today();

        // 1. Total jenis barang
        $totalJenisBarang = Barang::where('status_aktif', 1)->count();

        // 2. Total stok barang
        $totalStokBarang = (float) Barang::where('status_aktif', 1)->sum('stok_saat_ini');

        // 3. Stok aman
        $barangAman = Barang::where('status_aktif', 1)
            ->whereColumn('stok_saat_ini', '>', 'stok_minimum')
            ->count();

        // 4. Stok menipis
        $barangMenipisCount = Barang::where('status_aktif', 1)
            ->where('stok_saat_ini', '>', 0)
            ->whereColumn('stok_saat_ini', '<=', 'stok_minimum')
            ->count();

        // 5. Stok habis
        $barangHabisCount = Barang::where('status_aktif', 1)
            ->where('stok_saat_ini', '<=', 0)
            ->count();

        // 6. Total transaksi penjualan
        $totalTransaksiPenjualan = Penjualan::where('status_penjualan', 'selesai')->count();

        // 7. Total pembelian
        $totalPembelian = (float) Pembelian::where('status_pembelian', 'diterima')->sum('total_pembelian');

        // 8. Total piutang pelanggan
        $totalPiutang = (float) Penjualan::where('status_penjualan', 'selesai')
            ->whereRaw('(total_harga - diskon) > total_bayar')
            ->sum(DB::raw('(total_harga - diskon) - total_bayar'));

        // 9. Total penjualan hari ini
        $totalPenjualanHariIni = (float) Penjualan::where('status_penjualan', 'selesai')
            ->whereDate('tanggal_penjualan', $today)
            ->sum(DB::raw('total_harga - diskon'));

        // 10. Rencana belanja belum selesai
        $rencanaBelanjaAktif = RencanaBelanja::whereIn('status_rencana', ['draf', 'dipesan'])->count();

        // Lists for dashboard alerts
        $daftarBarangHabis = Barang::where('status_aktif', 1)
            ->where('stok_saat_ini', '<=', 0)
            ->with(['satuan', 'kategori'])
            ->limit(10)
            ->get();

        $daftarBarangMenipis = Barang::where('status_aktif', 1)
            ->where('stok_saat_ini', '>', 0)
            ->whereColumn('stok_saat_ini', '<=', 'stok_minimum')
            ->with(['satuan', 'kategori'])
            ->limit(10)
            ->get();

        $utangJatuhTempo = Penjualan::with('pelanggan')
            ->where('status_penjualan', 'selesai')
            ->whereRaw('(total_harga - diskon) > total_bayar')
            ->orderByRaw('CASE WHEN tanggal_jatuh_tempo IS NOT NULL AND tanggal_jatuh_tempo <= ? THEN 0 ELSE 1 END', [$today])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $aktivitasTerbaru = RiwayatAktivitas::with('pengguna')
            ->orderBy('dibuat_pada', 'desc')
            ->limit(8)
            ->get();

        $rencanaTerbaru = RencanaBelanja::with(['pengguna', 'detailRencanaBelanja.barang'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Chart 1: Penjualan 7 Hari Terakhir
        $chartHarianLabels = [];
        $chartHarianData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartHarianLabels[] = $date->translatedFormat('d M');
            $val = (float) Penjualan::where('status_penjualan', 'selesai')
                ->whereDate('tanggal_penjualan', $date)
                ->sum(DB::raw('total_harga - diskon'));
            $chartHarianData[] = $val;
        }

        // Chart 2: Penjualan 6 Bulan Terakhir
        $chartBulananLabels = [];
        $chartBulananData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $chartBulananLabels[] = $month->translatedFormat('M Y');
            $val = (float) Penjualan::where('status_penjualan', 'selesai')
                ->whereYear('tanggal_penjualan', $month->year)
                ->whereMonth('tanggal_penjualan', $month->month)
                ->sum(DB::raw('total_harga - diskon'));
            $chartBulananData[] = $val;
        }

        // Chart 3: Top 5 Barang Terlaris
        $topProducts = DetailPenjualan::select('barang_id', DB::raw('SUM(detail_penjualan.jumlah) as total_qty'))
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->where('penjualan.status_penjualan', 'selesai')
            ->groupBy('barang_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('barang')
            ->get();

        $topProductLabels = [];
        $topProductData = [];
        foreach ($topProducts as $tp) {
            $topProductLabels[] = $tp->barang?->nama_barang ?? 'Barang #' . $tp->barang_id;
            $topProductData[] = (float) $tp->total_qty;
        }

        // Chart 4: Barang Paling Sering Habis
        $mostOutOfStock = RiwayatStokHabis::select('barang_id', DB::raw('COUNT(id) as total_habis'))
            ->groupBy('barang_id')
            ->orderByDesc('total_habis')
            ->limit(5)
            ->with('barang')
            ->get();

        $outOfStockLabels = [];
        $outOfStockData = [];
        foreach ($mostOutOfStock as $mo) {
            $outOfStockLabels[] = $mo->barang?->nama_barang ?? 'Barang #' . $mo->barang_id;
            $outOfStockData[] = (int) $mo->total_habis;
        }

        // Chart 5: Status Piutang (Lunas vs Belum Lunas)
        $penjualanLunasCount = Penjualan::where('status_penjualan', 'selesai')
            ->whereRaw('(total_harga - diskon) <= total_bayar')
            ->count();
        $penjualanBelumLunasCount = Penjualan::where('status_penjualan', 'selesai')
            ->whereRaw('(total_harga - diskon) > total_bayar')
            ->count();

        // Kasir-specific metrics for Dashboard Kasir (Transaksi)
        $penjualanHariIniCount = Penjualan::where('status_penjualan', 'selesai')
            ->whereDate('tanggal_penjualan', $today)
            ->count();

        $kasMasukHariIni = (float) \App\Models\PembayaranPenjualan::whereDate('tanggal_pembayaran', $today)
            ->sum('jumlah_bayar');

        $transaksiUtangHariIniCount = Penjualan::where('status_penjualan', 'selesai')
            ->whereDate('tanggal_penjualan', $today)
            ->whereRaw('(total_harga - diskon) > total_bayar')
            ->count();

        // Fallback jika hari ini belum ada transaksi agar statistik kasir tidak kosong / 0
        if ($penjualanHariIniCount === 0) {
            $latestSalesDate = Penjualan::where('status_penjualan', 'selesai')
                ->latest('tanggal_penjualan')
                ->value(DB::raw('DATE(tanggal_penjualan)'));
            if ($latestSalesDate) {
                $penjualanHariIniCount = Penjualan::where('status_penjualan', 'selesai')
                    ->whereDate('tanggal_penjualan', $latestSalesDate)
                    ->count();

                $totalPenjualanHariIni = (float) Penjualan::where('status_penjualan', 'selesai')
                    ->whereDate('tanggal_penjualan', $latestSalesDate)
                    ->sum(DB::raw('total_harga - diskon'));

                $kasMasukHariIni = (float) \App\Models\PembayaranPenjualan::whereDate('tanggal_pembayaran', $latestSalesDate)
                    ->sum('jumlah_bayar');

                $transaksiUtangHariIniCount = Penjualan::where('status_penjualan', 'selesai')
                    ->whereDate('tanggal_penjualan', $latestSalesDate)
                    ->whereRaw('(total_harga - diskon) > total_bayar')
                    ->count();
            }
        }

        $transaksiTerbaruHariIni = Penjualan::with(['pelanggan', 'pengguna'])
            ->where('status_penjualan', 'selesai')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Prediksi Kebutuhan Belanja Moving Average untuk Role Admin
        $prediksiKebutuhanList = $this->forecastService->forecastAll(30, 7)
            ->where('rekomendasi_pembelian', '>', 0)
            ->sortByDesc('rekomendasi_pembelian')
            ->values();

        $totalItemPerluBeli = $prediksiKebutuhanList->count();
        $totalEstimasiBiayaPrediksi = (float) $prediksiKebutuhanList->sum('estimasi_biaya');
        $topPrediksiKebutuhan = $prediksiKebutuhanList->take(5);

        return view('dashboard.index', compact(
            'totalJenisBarang',
            'totalStokBarang',
            'barangAman',
            'barangMenipisCount',
            'barangHabisCount',
            'totalTransaksiPenjualan',
            'totalPembelian',
            'totalPiutang',
            'totalPenjualanHariIni',
            'rencanaBelanjaAktif',
            'daftarBarangHabis',
            'daftarBarangMenipis',
            'utangJatuhTempo',
            'aktivitasTerbaru',
            'rencanaTerbaru',
            'chartHarianLabels',
            'chartHarianData',
            'chartBulananLabels',
            'chartBulananData',
            'topProductLabels',
            'topProductData',
            'outOfStockLabels',
            'outOfStockData',
            'penjualanLunasCount',
            'penjualanBelumLunasCount',
            'penjualanHariIniCount',
            'kasMasukHariIni',
            'transaksiUtangHariIniCount',
            'transaksiTerbaruHariIni',
            'totalItemPerluBeli',
            'totalEstimasiBiayaPrediksi',
            'topPrediksiKebutuhan'
        ));
    }
}
