<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $peran = $request->input('peran');

        $query = Pengguna::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }
        if ($peran) {
            $query->where('peran', $peran);
        }

        $pengguna = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('pengguna.index', compact('pengguna', 'search', 'peran'));
    }

    public function create()
    {
        return view('pengguna.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:pengguna,email',
            'kata_sandi' => 'required|string|min:6',
            'peran' => 'required|in:admin,kasir',
            'status_aktif' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama pengguna wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter.',
            'peran.required' => 'Pilih peran pengguna.',
        ]);

        $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        $validated['status_aktif'] = $request->boolean('status_aktif', true);

        $pengguna = Pengguna::create($validated);
        ActivityLogService::log('tambah_pengguna', 'pengguna', $pengguna->id, "Menambahkan pengguna baru: {$pengguna->nama} ({$pengguna->email}) - Peran: {$pengguna->peran}");

        return redirect()->route('pengguna.index')->with('success', "Pengguna {$pengguna->nama} berhasil didaftarkan!");
    }

    public function edit(Pengguna $pengguna)
    {
        return view('pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('pengguna', 'email')->ignore($pengguna->id),
            ],
            'kata_sandi' => 'nullable|string|min:6',
            'peran' => 'required|in:admin,kasir',
            'status_aktif' => 'nullable|boolean',
        ]);

        if (!empty($validated['kata_sandi'])) {
            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        } else {
            unset($validated['kata_sandi']);
        }

        $validated['status_aktif'] = $request->boolean('status_aktif', true);

        // Safeguard: Cannot deactivate self
        if ($pengguna->id === Auth::id() && !$validated['status_aktif']) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $pengguna->update($validated);
        ActivityLogService::log('ubah_pengguna', 'pengguna', $pengguna->id, "Memperbarui data pengguna: {$pengguna->nama}");

        return redirect()->route('pengguna.index')->with('success', "Data pengguna {$pengguna->nama} berhasil diperbarui!");
    }

    public function destroy(Pengguna $pengguna)
    {
        if ($pengguna->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $hasActivity = $pengguna->penjualan()->exists() || $pengguna->pembelian()->exists() || $pengguna->riwayatStok()->exists();

        if ($hasActivity) {
            $pengguna->update(['status_aktif' => 0]);
            ActivityLogService::log('nonaktif_pengguna', 'pengguna', $pengguna->id, "Menonaktifkan pengguna {$pengguna->nama} karena memiliki riwayat aktivitas.");
            return back()->with('info', "Pengguna {$pengguna->nama} memiliki data transaksi, akun dinonaktifkan.");
        }

        $nama = $pengguna->nama;
        $pengguna->delete();
        ActivityLogService::log('hapus_pengguna', 'pengguna', null, "Menghapus pengguna {$nama}");

        return redirect()->route('pengguna.index')->with('success', "Pengguna {$nama} berhasil dihapus.");
    }
}
