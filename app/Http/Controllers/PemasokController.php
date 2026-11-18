<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Pemasok::withCount('pembelian');

        if ($search) {
            $query->where('nama_pemasok', 'like', "%{$search}%")
                ->orWhere('nomor_telepon', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $pemasok = $query->orderBy('nama_pemasok')->paginate(15)->withQueryString();

        return view('pemasok.index', compact('pemasok', 'search'));
    }

    public function create()
    {
        return view('pemasok.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemasok' => 'required|string|max:150',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:150',
        ], [
            'nama_pemasok.required' => 'Nama pemasok wajib diisi.',
            'email.email' => 'Format email pemasok tidak valid.',
        ]);

        $pemasok = Pemasok::create($validated);
        ActivityLogService::log('tambah_pemasok', 'pemasok', $pemasok->id, "Menambahkan pemasok: {$pemasok->nama_pemasok}");

        return redirect()->route('pemasok.index')->with('success', "Pemasok {$pemasok->nama_pemasok} berhasil ditambahkan!");
    }

    public function show(Pemasok $pemasok)
    {
        $pemasok->load(['pembelian' => fn($q) => $q->orderBy('tanggal_pembelian', 'desc')->limit(20)]);
        $totalTransaksi = $pemasok->pembelian()->where('status_pembelian', 'diterima')->sum('total_pembelian');

        return view('pemasok.show', compact('pemasok', 'totalTransaksi'));
    }

    public function edit(Pemasok $pemasok)
    {
        return view('pemasok.edit', compact('pemasok'));
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        $validated = $request->validate([
            'nama_pemasok' => 'required|string|max:150',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:150',
        ]);

        $pemasok->update($validated);
        ActivityLogService::log('ubah_pemasok', 'pemasok', $pemasok->id, "Memperbarui data pemasok: {$pemasok->nama_pemasok}");

        return redirect()->route('pemasok.show', $pemasok->id)->with('success', "Data pemasok {$pemasok->nama_pemasok} berhasil diperbarui!");
    }

    public function destroy(Pemasok $pemasok)
    {
        if ($pemasok->pembelian()->exists()) {
            return back()->with('error', "Pemasok '{$pemasok->nama_pemasok}' tidak dapat dihapus karena memiliki riwayat transaksi pembelian.");
        }

        $nama = $pemasok->nama_pemasok;
        $pemasok->delete();
        ActivityLogService::log('hapus_pemasok', 'pemasok', null, "Menghapus pemasok: {$nama}");

        return redirect()->route('pemasok.index')->with('success', "Pemasok '{$nama}' berhasil dihapus.");
    }
}
