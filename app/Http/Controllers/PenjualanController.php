<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Services\SalesService;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function __construct(
        protected SalesService $salesService
    ) {}

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $pelangganId = $request->input('pelanggan_id');
        $statusPenjualan = $request->input('status_penjualan');
        $search = $request->input('search');

        $query = Penjualan::with(['pelanggan', 'pengguna', 'detailPenjualan'])
            ->orderBy('tanggal_penjualan', 'desc');

        if ($search) {
            $query->where('nomor_faktur', 'like', "%{$search}%");
        }
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

        $penjualan = $query->paginate(15)->withQueryString();
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();

        return view('penjualan.index', compact('penjualan', 'pelanggans', 'startDate', 'endDate', 'pelangganId', 'statusPenjualan', 'search'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        // Load active items that have stock or can be ordered
        $barangs = Barang::where('status_aktif', 1)
            ->with(['satuan', 'kategori'])
            ->orderBy('nama_barang')
            ->get();

        $suggestedInvoice = $this->salesService->generateInvoiceNumber();

        return view('penjualan.create', compact('pelanggans', 'barangs', 'suggestedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_faktur' => 'nullable|string|max:100|unique:penjualan,nomor_faktur',
            'pelanggan_id' => 'nullable|exists:pelanggan,id',
            'tanggal_penjualan' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date|after_or_equal:tanggal_penjualan',
            'diskon' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,lainnya',
            'status_penjualan' => 'required|in:draf,selesai',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|numeric|min:0.001',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ], [
            'items.required' => 'Pilih minimal satu barang untuk transaksi.',
            'items.*.jumlah.min' => 'Jumlah barang harus lebih dari 0.',
            'tanggal_jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal penjualan.',
        ]);

        try {
            $penjualan = $this->salesService->createSale($validated);

            return redirect()->route('penjualan.show', $penjualan->id)
                ->with('success', "Transaksi penjualan {$penjualan->nomor_faktur} berhasil disimpan!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['pelanggan', 'pengguna', 'detailPenjualan.barang.satuan', 'pembayaranPenjualan.pengguna']);

        return view('penjualan.show', compact('penjualan'));
    }

    public function printReceipt(Penjualan $penjualan)
    {
        $penjualan->load(['pelanggan', 'pengguna', 'detailPenjualan.barang.satuan', 'pembayaranPenjualan']);

        return view('penjualan.print', compact('penjualan'));
    }

    public function cancel(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ], [
            'alasan.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        try {
            $this->salesService->cancelSale($penjualan->id, $request->input('alasan'));

            return redirect()->route('penjualan.show', $penjualan->id)
                ->with('success', "Transaksi {$penjualan->nomor_faktur} berhasil dibatalkan dan stok telah dikembalikan.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
