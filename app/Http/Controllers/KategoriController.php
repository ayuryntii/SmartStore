<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriBarang::withCount('barang')->orderBy('nama_kategori')->get();
        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_barang,nama_kategori',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori ini sudah terdaftar.',
        ]);

        $kategori = KategoriBarang::create($validated);
        ActivityLogService::log('tambah_kategori', 'kategori_barang', $kategori->id, "Menambahkan kategori: {$kategori->nama_kategori}");

        return redirect()->route('kategori.index')->with('success', "Kategori {$kategori->nama_kategori} berhasil ditambahkan!");
    }

    public function update(Request $request, KategoriBarang $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori_barang', 'nama_kategori')->ignore($kategori->id),
            ],
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($validated);
        ActivityLogService::log('ubah_kategori', 'kategori_barang', $kategori->id, "Memperbarui kategori: {$kategori->nama_kategori}");

        return redirect()->route('kategori.index')->with('success', "Kategori {$kategori->nama_kategori} berhasil diperbarui!");
    }

    public function destroy(KategoriBarang $kategori)
    {
        if ($kategori->barang()->exists()) {
            return back()->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh produk barang.");
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();
        ActivityLogService::log('hapus_kategori', 'kategori_barang', null, "Menghapus kategori: {$nama}");

        return redirect()->route('kategori.index')->with('success', "Kategori '{$nama}' berhasil dihapus.");
    }
}
