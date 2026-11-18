<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStok extends Model
{
    use HasFactory;

    protected $table = 'riwayat_stok';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'barang_id',
        'batch_id',
        'pengguna_id',
        'jenis_pergerakan',
        'jumlah',
        'jenis_referensi',
        'referensi_id',
        'catatan',
        'tanggal_pergerakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pergerakan' => 'datetime',
            'jumlah' => 'decimal:3',
        ];
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function batch()
    {
        return $this->belongsTo(BatchBarang::class, 'batch_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function getJenisPergerakanLabelAttribute(): string
    {
        return match ($this->jenis_pergerakan) {
            'pembelian_masuk' => 'Pembelian Masuk',
            'penjualan_keluar' => 'Penjualan Keluar',
            'penyesuaian_masuk' => 'Penyesuaian Masuk',
            'penyesuaian_keluar' => 'Penyesuaian Keluar',
            'retur_masuk' => 'Retur Masuk',
            'retur_keluar' => 'Retur Keluar',
            default => ucfirst(str_replace('_', ' ', $this->jenis_pergerakan)),
        };
    }
}
