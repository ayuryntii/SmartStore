<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $pemasokId = $request->input('pemasok_id');
        $statusPembelian = $request->input('status_pembelian');
        $search = $request->input('search');

        $query = Pembelian::with(['pemasok', 'pengguna', 'detailPembelian'])
            ->orderBy('tanggal_pembelian', 'desc');

        if ($search) {
            $query->where('nomor_faktur', 'like', "%{$search}%");
        }
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

        $pembelian = $query->paginate(15)->withQueryString();
        $pemasoks = Pemasok::orderBy('nama_pemasok')->get();

        return view('pembelian.index', compact('pembelian', 'pemasoks', 'startDate', 'endDate', 'pemasokId', 'statusPembelian', 'search'));
    }

    public function create()
    {
        $pemasoks = Pemasok::orderBy('nama_pemasok')->get();
        $barangs = Barang::where('status_aktif', 1)->with('satuan')->orderBy('nama_barang')->get();
        $suggestedInvoice = $this->purchaseService->generateInvoiceNumber();

        return view('pembelian.create', compact('pemasoks', 'barangs', 'suggestedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pemasok_id' => 'required|exists:pemasok,id',
            'nomor_faktur' => 'nullable|string|max:100|unique:pembelian,nomor_faktur',
            'tanggal_pembelian' => 'required|date',
            'status_pembelian' => 'required|in:draf,diterima',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|numeric|min:0.001',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.kode_batch' => 'nullable|string|max:100',
            'items.*.tanggal_kedaluwarsa' => 'nullable|date',
        ], [
            'pemasok_id.required' => 'Pemasok wajib dipilih.',
            'items.required' => 'Masukkan minimal satu barang pembelian.',
            'items.*.jumlah.min' => 'Jumlah barang harus lebih dari 0.',
        ]);

        try {
            $pembelian = $this->purchaseService->createPurchase($validated);

            return redirect()->route('pembelian.show', $pembelian->id)
                ->with('success', "Transaksi pembelian {$pembelian->nomor_faktur} berhasil dicatat!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['pemasok', 'pengguna', 'detailPembelian.barang.satuan', 'detailPembelian.batchBarang']);

        return view('pembelian.show', compact('pembelian'));
    }

    public function receive(Pembelian $pembelian)
    {
        try {
            $this->purchaseService->receivePurchase($pembelian->id);

            return redirect()->route('pembelian.show', $pembelian->id)
                ->with('success', "Barang untuk faktur {$pembelian->nomor_faktur} berhasil diterima dan stok telah bertambah!");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
