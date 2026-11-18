<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Satuan;
use App\Services\ActivityLogService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori_id');
        $statusStok = $request->input('status_stok');
        $statusAktif = $request->input('status_aktif', 1);

        $query = Barang::with(['kategori', 'satuan', 'batchBarang']);

        if (!is_null($statusAktif) && $statusAktif !== '') {
            $query->where('status_aktif', $statusAktif);
        }

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

        $barang = $query->orderBy('nama_barang')->paginate(15)->withQueryString();
        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();

        return view('barang.index', compact('barang', 'kategoris', 'search', 'kategoriId', 'statusStok', 'statusAktif'));
    }

    public function create()
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berwenang menambah data barang.');
        }

        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        // Generate suggested item code: BRG-XXXX
        $lastId = Barang::max('id') ?? 0;
        $suggestedCode = 'BRG-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        return view('barang.create', compact('kategoris', 'satuans', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berwenang menambah data barang.');
        }

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori_barang,id',
            'satuan_id' => 'required|exists:satuan,id',
            'deskripsi' => 'nullable|string',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'stok_awal' => 'nullable|numeric|min:0',
            'status_aktif' => 'nullable|boolean',
        ], [
            'kode_barang.unique' => 'Kode barang sudah digunakan. Masukkan kode lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'satuan_id.required' => 'Satuan wajib dipilih.',
            'harga_beli.min' => 'Harga beli tidak boleh negatif.',
            'harga_jual.min' => 'Harga jual tidak boleh negatif.',
            'stok_minimum.min' => 'Stok minimum tidak boleh negatif.',
        ]);

        $stokAwal = (float) ($validated['stok_awal'] ?? 0);
        unset($validated['stok_awal']);
        $validated['status_aktif'] = $request->boolean('status_aktif', true);
        $validated['stok_saat_ini'] = 0; // Will be set via StockService

        $barang = DB::transaction(function () use ($validated, $stokAwal) {
            $barang = Barang::create($validated);
            $this->stockService->setInitialStock($barang, $stokAwal);

            ActivityLogService::log(
                'tambah_barang',
                'barang',
                $barang->id,
                "Menambahkan barang baru: {$barang->nama_barang} (Kode: {$barang->kode_barang})"
            );

            return $barang;
        });

        return redirect()->route('barang.show', $barang->id)
            ->with('success', "Barang {$barang->nama_barang} berhasil ditambahkan!");
    }

    public function show(Barang $barang)
    {
        $barang->load([
            'kategori',
            'satuan',
            'batchBarang' => fn($q) => $q->orderBy('sisa_jumlah', 'desc')->orderBy('tanggal_kedaluwarsa', 'asc'),
            'riwayatStok' => fn($q) => $q->with('pengguna')->limit(20),
            'riwayatStokHabis' => fn($q) => $q->limit(10),
        ]);

        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berwenang mengubah data barang.');
        }

        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('barang.edit', compact('barang', 'kategoris', 'satuans'));
    }

    public function update(Request $request, Barang $barang)
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berwenang mengubah data barang.');
        }

        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barang', 'kode_barang')->ignore($barang->id),
            ],
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori_barang,id',
            'satuan_id' => 'required|exists:satuan,id',
            'deskripsi' => 'nullable|string',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'status_aktif' => 'nullable|boolean',
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif', true);

        $barang->update($validated);

        ActivityLogService::log(
            'ubah_barang',
            'barang',
            $barang->id,
            "Memperbarui data barang: {$barang->nama_barang}"
        );

        return redirect()->route('barang.show', $barang->id)
            ->with('success', "Data barang {$barang->nama_barang} berhasil diperbarui!");
    }

    public function destroy(Barang $barang)
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berwenang menghapus data barang.');
        }

        // Safe toggle or delete
        $hasTransactions = $barang->detailPenjualan()->exists() || $barang->detailPembelian()->exists();

        if ($hasTransactions) {
            $barang->update(['status_aktif' => 0]);
            ActivityLogService::log(
                'nonaktif_barang',
                'barang',
                $barang->id,
                "Menonaktifkan barang {$barang->nama_barang} karena memiliki riwayat transaksi."
            );
            return redirect()->route('barang.index')
                ->with('info', "Barang {$barang->nama_barang} memiliki riwayat transaksi, status diubah menjadi Nonaktif.");
        }

        $nama = $barang->nama_barang;
        $barang->delete();
        ActivityLogService::log('hapus_barang', 'barang', null, "Menghapus barang {$nama}");

        return redirect()->route('barang.index')
            ->with('success', "Barang {$nama} berhasil dihapus.");
    }

    /**
     * Endpoint for "Tombol Laporkan Barang Habis"
     */
    public function reportOutOfStock(Request $request, Barang $barang)
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berwenang melaporkan status barang habis secara manual.');
        }

        $request->validate([
            'alasan' => 'required|string|max:255',
        ], [
            'alasan.required' => 'Keterangan atau alasan fisik habis wajib diisi.',
        ]);

        $result = $this->stockService->reportOutOfStock($barang->id, $request->input('alasan'));

        return back()->with('success', $result['pesan']);
    }
}
