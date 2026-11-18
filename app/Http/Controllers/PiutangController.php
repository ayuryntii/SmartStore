<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Services\DebtPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function __construct(
        protected DebtPaymentService $debtPaymentService
    ) {}

    public function index(Request $request)
    {
        $pelangganId = $request->input('pelanggan_id');
        $statusFilter = $request->input('status_pembayaran'); // 'belum_dibayar', 'dibayar_sebagian', 'jatuh_tempo', 'lunas'

        $query = Penjualan::with(['pelanggan', 'pembayaranPenjualan'])
            ->where('status_penjualan', 'selesai')
            ->orderBy('tanggal_penjualan', 'desc');

        if ($pelangganId) {
            $query->where('pelanggan_id', $pelangganId);
        }

        $allSales = $query->get();

        // Filter by derived status
        if ($statusFilter) {
            $filtered = $allSales->filter(function ($s) use ($statusFilter) {
                return $s->status_pembayaran === $statusFilter;
            });
        } else {
            // Default: show unpaid / partially paid / overdue first
            $filtered = $allSales;
        }

        // Metrics
        $totalSemuaPiutang = (float) $allSales->sum(fn($s) => $s->sisa_tagihan);
        $totalJatuhTempo = (float) $allSales->filter(fn($s) => $s->status_pembayaran === 'jatuh_tempo')->sum(fn($s) => $s->sisa_tagihan);
        $totalLunasCount = $allSales->filter(fn($s) => $s->status_pembayaran === 'lunas')->count();
        $totalBelumLunasCount = $allSales->filter(fn($s) => $s->sisa_tagihan > 0.001)->count();

        // Pagination for filtered collection
        $page = $request->input('page', 1);
        $perPage = 15;
        $items = $filtered->slice(($page - 1) * $perPage, $perPage)->values();
        $piutangs = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();

        return view('piutang.index', compact(
            'piutangs',
            'pelanggans',
            'pelangganId',
            'statusFilter',
            'totalSemuaPiutang',
            'totalJatuhTempo',
            'totalLunasCount',
            'totalBelumLunasCount'
        ));
    }

    public function show(Pelanggan $pelanggan)
    {
        $sales = Penjualan::where('pelanggan_id', $pelanggan->id)
            ->where('status_penjualan', 'selesai')
            ->with(['detailPenjualan.barang.satuan', 'pembayaranPenjualan.pengguna'])
            ->orderBy('tanggal_penjualan', 'desc')
            ->get();

        $totalUtang = $sales->sum(fn($s) => $s->sisa_tagihan);
        $totalDibayar = $sales->sum(fn($s) => (float) $s->total_bayar);

        return view('piutang.show', compact('pelanggan', 'sales', 'totalUtang', 'totalDibayar'));
    }

    public function bayar(Request $request)
    {
        $validated = $request->validate([
            'penjualan_id' => 'required|exists:penjualan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,lainnya',
            'tanggal_pembayaran' => 'required|date',
            'catatan' => 'nullable|string|max:255',
        ], [
            'jumlah_bayar.min' => 'Jumlah bayar minimal Rp 1.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran.',
        ]);

        try {
            $this->debtPaymentService->payInvoice(
                (int) $validated['penjualan_id'],
                (float) $validated['jumlah_bayar'],
                $validated['metode_pembayaran'],
                $validated['tanggal_pembayaran'],
                $validated['catatan']
            );

            return back()->with('success', 'Pembayaran utang berhasil dicatat!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function bayarPelanggan(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,lainnya',
            'catatan' => 'nullable|string|max:255',
        ]);

        try {
            $this->debtPaymentService->payCustomerDebt(
                $pelanggan->id,
                (float) $validated['jumlah_bayar'],
                $validated['metode_pembayaran'],
                $validated['catatan']
            );

            return back()->with('success', "Pelunasan utang pelanggan {$pelanggan->nama_pelanggan} berhasil dicatat!");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
