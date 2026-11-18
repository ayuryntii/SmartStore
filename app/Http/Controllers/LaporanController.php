<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\RencanaBelanja;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $jenis = $request->input('jenis', 'penjualan');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $kategoriId = $request->input('kategori_id');
        $pelangganId = $request->input('pelanggan_id');
        $pemasokId = $request->input('pemasok_id');

        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $pemasoks = Pemasok::orderBy('nama_pemasok')->get();

        $data = $this->fetchReportData($jenis, $startDate, $endDate, $kategoriId, $pelangganId, $pemasokId);

        return view('laporan.index', compact(
            'jenis',
            'startDate',
            'endDate',
            'kategoriId',
            'pelangganId',
            'pemasokId',
            'kategoris',
            'pelanggans',
            'pemasoks',
            'data'
        ));
    }

    public function exportPdf(Request $request)
    {
        $jenis = $request->input('jenis', 'penjualan');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $kategoriId = $request->input('kategori_id');
        $pelangganId = $request->input('pelanggan_id');
        $pemasokId = $request->input('pemasok_id');

        $data = $this->fetchReportData($jenis, $startDate, $endDate, $kategoriId, $pelangganId, $pemasokId);

        $pdf = Pdf::loadView('laporan.pdf', compact('jenis', 'startDate', 'endDate', 'data'))
            ->setPaper('a4', 'portrait');

        $filename = "Laporan_{$jenis}_" . date('Ymd_His') . ".pdf";
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $jenis = $request->input('jenis', 'penjualan');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $kategoriId = $request->input('kategori_id');
        $pelangganId = $request->input('pelanggan_id');
        $pemasokId = $request->input('pemasok_id');

        $data = $this->fetchReportData($jenis, $startDate, $endDate, $kategoriId, $pelangganId, $pemasokId);

        $filename = "Laporan_{$jenis}_" . date('Ymd_His') . ".csv";

        return match ($jenis) {
            'penjualan' => $this->exportPenjualanCsv($data, $filename),
            'pembelian' => $this->exportPembelianCsv($data, $filename),
            'stok_saat_ini', 'stok_minimum', 'barang_habis' => $this->exportStokCsv($data, $filename),
            'piutang_pelanggan' => $this->exportPiutangCsv($data, $filename),
            'keuangan_sederhana' => $this->exportKeuanganCsv($data, $filename),
            default => $this->exportGenericCsv($data, $filename),
        };
    }

    protected function fetchReportData(
        string $jenis,
        ?string $startDate,
        ?string $endDate,
        ?int $kategoriId,
        ?int $pelangganId,
        ?int $pemasokId
    ) {
        return match ($jenis) {
            'barang' => $this->reportService->getBarangReport($kategoriId),
            'stok_saat_ini' => $this->reportService->getStockReport('semua', $kategoriId),
            'stok_minimum' => $this->reportService->getStockReport('menipis', $kategoriId),
            'barang_habis' => $this->reportService->getStockReport('habis', $kategoriId),
            'barang_masuk' => $this->reportService->getStockMovementReport('masuk', $startDate, $endDate),
            'barang_keluar' => $this->reportService->getStockMovementReport('keluar', $startDate, $endDate),
            'penjualan' => $this->reportService->getSalesReport($startDate, $endDate, $pelangganId),
            'pembelian' => $this->reportService->getPurchaseReport($startDate, $endDate, $pemasokId),
            'rencana_belanja' => RencanaBelanja::with(['pengguna', 'detailRencanaBelanja.barang.satuan'])->orderBy('tanggal_rencana', 'desc')->get(),
            'piutang_pelanggan' => $this->reportService->getDebtReport($pelangganId),
            'pembayaran_utang' => $this->reportService->getDebtPaymentsReport($startDate, $endDate, $pelangganId),
            'barang_terlaris' => $this->reportService->getTopAndSlowProductsReport($startDate, $endDate)['terlaris'],
            'barang_sering_habis' => $this->reportService->getFrequentlyOutOfStockReport($startDate, $endDate),
            'barang_kedaluwarsa' => $this->reportService->getExpiryReport(),
            'keuangan_sederhana' => $this->reportService->getFinancialReport($startDate, $endDate),
            default => collect(),
        };
    }

    protected function exportPenjualanCsv($data, string $filename)
    {
        $headers = ['No Faktur', 'Tanggal', 'Pelanggan', 'Kasir', 'Total Harga', 'Diskon', 'Total Tagihan', 'Total Bayar', 'Sisa Utang', 'Status'];
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item->nomor_faktur,
                $item->tanggal_penjualan->format('Y-m-d H:i'),
                $item->pelanggan?->nama_pelanggan ?? 'Umum',
                $item->pengguna?->nama ?? '-',
                $item->total_harga,
                $item->diskon,
                $item->total_tagihan,
                $item->total_bayar,
                $item->sisa_tagihan,
                $item->status_pembayaran_label,
            ];
        }
        return $this->reportService->exportCsv($filename, $headers, $rows);
    }

    protected function exportPembelianCsv($data, string $filename)
    {
        $headers = ['No Faktur', 'Tanggal', 'Pemasok', 'Petugas', 'Total Pembelian', 'Status'];
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item->nomor_faktur,
                $item->tanggal_pembelian->format('Y-m-d H:i'),
                $item->pemasok?->nama_pemasok ?? '-',
                $item->pengguna?->nama ?? '-',
                $item->total_pembelian,
                ucfirst($item->status_pembelian),
            ];
        }
        return $this->reportService->exportCsv($filename, $headers, $rows);
    }

    protected function exportStokCsv($data, string $filename)
    {
        $headers = ['Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Harga Beli', 'Harga Jual', 'Stok Minimum', 'Stok Saat Ini', 'Status Stok'];
        $rows = [];
        foreach ($data as $b) {
            $rows[] = [
                $b->kode_barang,
                $b->nama_barang,
                $b->kategori?->nama_kategori ?? '-',
                $b->satuan?->singkatan ?? '-',
                $b->harga_beli,
                $b->harga_jual,
                $b->stok_minimum,
                $b->stok_saat_ini,
                $b->status_stok_label,
            ];
        }
        return $this->reportService->exportCsv($filename, $headers, $rows);
    }

    protected function exportPiutangCsv($data, string $filename)
    {
        $headers = ['No Faktur', 'Tanggal Penjualan', 'Jatuh Tempo', 'Pelanggan', 'Total Tagihan', 'Sudah Dibayar', 'Sisa Utang', 'Status Pembayaran'];
        $rows = [];
        foreach ($data as $s) {
            $rows[] = [
                $s->nomor_faktur,
                $s->tanggal_penjualan->format('Y-m-d'),
                $s->tanggal_jatuh_tempo ? $s->tanggal_jatuh_tempo->format('Y-m-d') : '-',
                $s->pelanggan?->nama_pelanggan ?? 'Umum',
                $s->total_tagihan,
                $s->total_bayar,
                $s->sisa_tagihan,
                $s->status_pembayaran_label,
            ];
        }
        return $this->reportService->exportCsv($filename, $headers, $rows);
    }

    protected function exportKeuanganCsv($data, string $filename)
    {
        $headers = ['Indikator Keuangan', 'Nilai (Rp)'];
        $rows = [
            ['Total Penjualan Kotor', $data['total_penjualan_kotor']],
            ['Total Diskon Diberikan', $data['total_diskon']],
            ['Total Penjualan Bersih', $data['total_penjualan_bersih']],
            ['Total Pembayaran Diterima (Kas Masuk Aktual)', $data['total_kas_masuk']],
            ['Total Pembelian Barang (Kas Keluar Pembelian)', $data['total_pembelian']],
            ['Total Piutang Belum Dibayar', $data['total_piutang_belum_dibayar']],
            ['Total Harga Pokok Penjualan (HPP)', $data['total_hpp']],
            ['Estimasi Laba Kotor', $data['estimasi_laba_kotor']],
            ['Arus Kas Bersih (Kas Masuk - Pembelian)', $data['arus_kas_bersih']],
        ];
        return $this->reportService->exportCsv($filename, $headers, $rows);
    }

    protected function exportGenericCsv($data, string $filename)
    {
        $headers = ['Data'];
        $rows = [];
        return $this->reportService->exportCsv($filename, $headers, $rows);
    }
}
