<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\RiwayatStok;
use App\Services\StockService;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function index(Request $request)
    {
        $statusStok = $request->input('status_stok');
        $kategoriId = $request->input('kategori_id');
        $search = $request->input('search');

        $query = Barang::with(['kategori', 'satuan'])->where('status_aktif', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($statusStok === 'habis') {
            $query->where('stok_saat_ini', '<=', 0);
        } elseif ($statusStok === 'menipis') {
            $query->where('stok_saat_ini', '>', 0)
                ->whereColumn('stok_saat_ini', '<=', 'stok_minimum');
        } elseif ($statusStok === 'aman') {
            $query->whereColumn('stok_saat_ini', '>', 'stok_minimum');
        }

        $barang = $query->orderBy('stok_saat_ini', 'asc')->paginate(15)->withQueryString();
        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();

        // Metrics
        $totalAman = Barang::where('status_aktif', 1)->whereColumn('stok_saat_ini', '>', 'stok_minimum')->count();
        $totalMenipis = Barang::where('status_aktif', 1)->where('stok_saat_ini', '>', 0)->whereColumn('stok_saat_ini', '<=', 'stok_minimum')->count();
        $totalHabis = Barang::where('status_aktif', 1)->where('stok_saat_ini', '<=', 0)->count();

        return view('stok.index', compact('barang', 'kategoris', 'statusStok', 'kategoriId', 'search', 'totalAman', 'totalMenipis', 'totalHabis'));
    }

    public function riwayat(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $jenisPergerakan = $request->input('jenis_pergerakan');
        $barangId = $request->input('barang_id');

        $query = RiwayatStok::with(['barang.satuan', 'pengguna', 'batch'])
            ->orderBy('tanggal_pergerakan', 'desc');

        if ($startDate) {
            $query->whereDate('tanggal_pergerakan', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_pergerakan', '<=', $endDate);
        }
        if ($jenisPergerakan) {
            $query->where('jenis_pergerakan', $jenisPergerakan);
        }
        if ($barangId) {
            $query->where('barang_id', $barangId);
        }

        $riwayat = $query->paginate(20)->withQueryString();
        $barangList = Barang::where('status_aktif', 1)->orderBy('nama_barang')->get();

        return view('stok.riwayat', compact('riwayat', 'barangList', 'startDate', 'endDate', 'jenisPergerakan', 'barangId'));
    }

    public function penyesuaianForm()
    {
        $barangList = Barang::where('status_aktif', 1)->with('satuan')->orderBy('nama_barang')->get();
        return view('stok.penyesuaian', compact('barangList'));
    }

    public function penyesuaianSubmit(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jenis_pergerakan' => 'required|in:penyesuaian_masuk,penyesuaian_keluar,retur_masuk,retur_keluar',
            'jumlah' => 'required|numeric|min:0.001',
            'catatan' => 'required|string|max:255',
        ], [
            'barang_id.required' => 'Barang wajib dipilih.',
            'jenis_pergerakan.required' => 'Jenis penyesuaian wajib dipilih.',
            'jumlah.min' => 'Jumlah harus lebih besar dari 0.',
            'catatan.required' => 'Alasan/keterangan penyesuaian wajib diisi.',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);
        $jumlah = (float) $validated['jumlah'];
        $jenis = $validated['jenis_pergerakan'];
        $catatan = $validated['catatan'];

        try {
            if (in_array($jenis, ['penyesuaian_masuk', 'retur_masuk'])) {
                $this->stockService->addStock(
                    $barang->id,
                    $jumlah,
                    $jenis,
                    $catatan,
                    'penyesuaian_manual',
                    null
                );
            } else {
                $this->stockService->reduceStock(
                    $barang->id,
                    $jumlah,
                    $jenis,
                    $catatan,
                    'penyesuaian_manual',
                    null
                );
            }

            return redirect()->route('stok.riwayat')
                ->with('success', "Penyesuaian stok untuk {$barang->nama_barang} berhasil dicatat!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
