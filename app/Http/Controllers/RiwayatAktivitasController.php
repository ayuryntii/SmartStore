<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\RiwayatAktivitas;
use Illuminate\Http\Request;

class RiwayatAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $penggunaId = $request->input('pengguna_id');
        $aksi = $request->input('aksi');

        $query = RiwayatAktivitas::with('pengguna')->orderBy('dibuat_pada', 'desc');

        if ($startDate) {
            $query->whereDate('dibuat_pada', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('dibuat_pada', '<=', $endDate);
        }
        if ($penggunaId) {
            $query->where('pengguna_id', $penggunaId);
        }
        if ($aksi) {
            $query->where('aksi', $aksi);
        }

        $aktivitas = $query->paginate(20)->withQueryString();
        $penggunas = Pengguna::orderBy('nama')->get();

        return view('aktivitas.index', compact('aktivitas', 'penggunas', 'startDate', 'endDate', 'penggunaId', 'aksi'));
    }
}
