<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_pelanggan',
        'nomor_telepon',
        'alamat',
    ];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'pelanggan_id');
    }

    /**
     * Total piutang pelanggan saat ini (penjualan selesai yang belum lunas)
     */
    public function getTotalPiutangAttribute(): float
    {
        return (float) $this->penjualan()
            ->where('status_penjualan', 'selesai')
            ->get()
            ->sum(function ($p) {
                return $p->sisa_tagihan;
            });
    }
}
