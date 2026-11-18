<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BatchBarang;
use App\Models\DetailPenjualan;
use App\Models\KategoriBarang;
use App\Models\RiwayatStokHabis;
use App\Services\ForecastService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalisisController extends Controller
{
    public function __construct(
        protected ForecastService $forecastService,
        protected ReportService $reportService
    ) {}

    /**
     * Section 5.L: Analisis Barang Paling Sering Habis
     */
    public function seringHabis(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periode = $request->input('periode', 'semua'); // 'harian', 'mingguan', 'bulanan', 'semua'

        if ($periode === 'mingguan') {
            $startDate = Carbon::now()->subWeeks(4)->toDateString();
            $endDate = Carbon::now()->toDateString();
        } elseif ($periode === 'bulanan') {
            $startDate = Carbon::now()->subMonths(6)->toDateString();
            $endDate = Carbon::now()->toDateString();
        } elseif ($periode === 'harian') {
            $startDate = Carbon::now()->subDays(7)->toDateString();
            $endDate = Carbon::now()->toDateString();
        }

        $analisis = $this->reportService->getFrequentlyOutOfStockReport($startDate, $endDate);

        // Chart data
        $chartLabels = [];
        $chartData = [];
        foreach ($analisis->take(10) as $item) {
            $chartLabels[] = $item['barang']?->nama_barang ?? 'Barang';
            $chartData[] = (int) $item['frekuensi_habis'];
        }

        return view('analisis.sering_habis', compact('analisis', 'chartLabels', 'chartData', 'startDate', 'endDate', 'periode'));
    }

    /**
     * Section 5.M: Analisis Barang Terlaris dan Kurang Laku
     */
    public function terlaris(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = $this->reportService->getTopAndSlowProductsReport($startDate, $endDate);
        $terlaris = $data['terlaris'];
        $kurangLaku = $data['kurang_laku'];

        // Chart: Top 10 Best Sellers
        $topLabels = [];
        $topQty = [];
        foreach ($terlaris->take(10) as $t) {
            $topLabels[] = $t['barang']?->nama_barang ?? 'Item';
            $topQty[] = (float) $t['total_terjual'];
        }

        return view('analisis.terlaris', compact('terlaris', 'kurangLaku', 'topLabels', 'topQty', 'startDate', 'endDate'));
    }

    /**
     * Section 5.N: Prediksi Kebutuhan Stok (Moving Average)
     */
    public function prediksi(Request $request)
    {
        $historyDays = (int) $request->input('history_days', 30);
        $planningDays = (int) $request->input('planning_days', 7);
        $kategoriId = $request->input('kategori_id');

        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();
        $forecasts = $this->forecastService->forecastAll($historyDays, $planningDays, $kategoriId);

        // Calculate total estimated budget needed
        $totalEstimasiBiaya = $forecasts->sum('estimasi_biaya');
        $totalItemPerluBeli = $forecasts->where('rekomendasi_pembelian', '>', 0)->count();

        return view('analisis.prediksi', compact(
            'forecasts',
            'kategoris',
            'historyDays',
            'planningDays',
            'kategoriId',
            'totalEstimasiBiaya',
            'totalItemPerluBeli'
        ));
    }

    /**
     * Section 5.O: Manajemen Kedaluwarsa (FEFO)
     */
    public function kedaluwarsa(Request $request)
    {
        $filter = $request->input('filter', 'semua');
        $batches = $this->reportService->getExpiryReport($filter);

        $now = now()->startOfDay();
        $totalSudahKedaluwarsa = BatchBarang::where('sisa_jumlah', '>', 0)
            ->whereNotNull('tanggal_kedaluwarsa')
            ->whereDate('tanggal_kedaluwarsa', '<', $now)
            ->count();

        $totalKritis7Hari = BatchBarang::where('sisa_jumlah', '>', 0)
            ->whereNotNull('tanggal_kedaluwarsa')
            ->whereBetween('tanggal_kedaluwarsa', [$now, $now->copy()->addDays(7)])
            ->count();

        $totalPeringatan30Hari = BatchBarang::where('sisa_jumlah', '>', 0)
            ->whereNotNull('tanggal_kedaluwarsa')
            ->whereBetween('tanggal_kedaluwarsa', [$now, $now->copy()->addDays(30)])
            ->count();

        return view('analisis.kedaluwarsa', compact(
            'batches',
            'filter',
            'totalSudahKedaluwarsa',
            'totalKritis7Hari',
            'totalPeringatan30Hari'
        ));
    }
}
