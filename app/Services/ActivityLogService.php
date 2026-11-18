<?php

namespace App\Services;

use App\Models\RiwayatAktivitas;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log user or system activity
     */
    public static function log(
        string $aksi,
        ?string $namaTabel = null,
        ?int $dataId = null,
        ?string $deskripsi = null,
        ?int $penggunaId = null
    ): RiwayatAktivitas {
        $ip = request()?->ip() ?? '127.0.0.1';
        $userId = $penggunaId ?? Auth::id();

        return RiwayatAktivitas::create([
            'pengguna_id' => $userId,
            'aksi' => $aksi,
            'nama_tabel' => $namaTabel,
            'data_id' => $dataId,
            'deskripsi' => $deskripsi,
            'alamat_ip' => $ip,
        ]);
    }
}
