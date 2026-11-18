<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SatuanController extends Controller
{
    public function index()
    {
        $satuan = Satuan::withCount('barang')->orderBy('nama_satuan')->get();
        return view('satuan.index', compact('satuan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan',
            'singkatan' => 'required|string|max:15',
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique' => 'Nama satuan ini sudah terdaftar.',
            'singkatan.required' => 'Singkatan wajib diisi (misal: pcs, kg, btl).',
        ]);

        $satuan = Satuan::create($validated);
        ActivityLogService::log('tambah_satuan', 'satuan', $satuan->id, "Menambahkan satuan: {$satuan->nama_satuan}");

        return redirect()->route('satuan.index')->with('success', "Satuan {$satuan->nama_satuan} berhasil ditambahkan!");
    }

    public function update(Request $request, Satuan $satuan)
    {
        $validated = $request->validate([
            'nama_satuan' => [
                'required',
                'string',
                'max:50',
                Rule::unique('satuan', 'nama_satuan')->ignore($satuan->id),
            ],
            'singkatan' => 'required|string|max:15',
        ]);

        $satuan->update($validated);
        ActivityLogService::log('ubah_satuan', 'satuan', $satuan->id, "Memperbarui satuan: {$satuan->nama_satuan}");

        return redirect()->route('satuan.index')->with('success', "Satuan {$satuan->nama_satuan} berhasil diperbarui!");
    }

    public function destroy(Satuan $satuan)
    {
        if ($satuan->barang()->exists()) {
            return back()->with('error', "Satuan '{$satuan->nama_satuan}' tidak dapat dihapus karena masih digunakan oleh produk barang.");
        }

        $nama = $satuan->nama_satuan;
        $satuan->delete();
        ActivityLogService::log('hapus_satuan', 'satuan', null, "Menghapus satuan: {$nama}");

        return redirect()->route('satuan.index')->with('success', "Satuan '{$nama}' berhasil dihapus.");
    }
}
