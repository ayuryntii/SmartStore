<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Pelanggan::withCount('penjualan');

        if ($search) {
            $query->where('nama_pelanggan', 'like', "%{$search}%")
                ->orWhere('nomor_telepon', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%");
        }

        $pelanggan = $query->orderBy('nama_pelanggan')->paginate(15)->withQueryString();

        return view('pelanggan.index', compact('pelanggan', 'search'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:150',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
        ]);

        $pelanggan = Pelanggan::create($validated);
        ActivityLogService::log('tambah_pelanggan', 'pelanggan', $pelanggan->id, "Menambahkan pelanggan: {$pelanggan->nama_pelanggan}");

        return redirect()->route('pelanggan.index')->with('success', "Pelanggan {$pelanggan->nama_pelanggan} berhasil ditambahkan!");
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load(['penjualan' => fn($q) => $q->orderBy('tanggal_penjualan', 'desc')->limit(20)]);
        $totalBelanja = $pelanggan->penjualan()->where('status_penjualan', 'selesai')->sum('total_bayar');
        $totalPiutang = $pelanggan->total_piutang;

        return view('pelanggan.show', compact('pelanggan', 'totalBelanja', 'totalPiutang'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:150',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $pelanggan->update($validated);
        ActivityLogService::log('ubah_pelanggan', 'pelanggan', $pelanggan->id, "Memperbarui data pelanggan: {$pelanggan->nama_pelanggan}");

        return redirect()->route('pelanggan.show', $pelanggan->id)->with('success', "Data pelanggan {$pelanggan->nama_pelanggan} berhasil diperbarui!");
    }

    public function destroy(Pelanggan $pelanggan)
    {
        if ($pelanggan->penjualan()->exists()) {
            return back()->with('error', "Pelanggan '{$pelanggan->nama_pelanggan}' tidak dapat dihapus karena memiliki riwayat transaksi penjualan.");
        }

        $nama = $pelanggan->nama_pelanggan;
        $pelanggan->delete();
        ActivityLogService::log('hapus_pelanggan', 'pelanggan', null, "Menghapus pelanggan: {$nama}");

        return redirect()->route('pelanggan.index')->with('success', "Pelanggan '{$nama}' berhasil dihapus.");
    }
}
