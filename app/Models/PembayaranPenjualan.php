<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPenjualan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_penjualan';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'penjualan_id',
        'pengguna_id',
        'jumlah_bayar',
        'metode_pembayaran',
        'tanggal_pembayaran',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pembayaran' => 'datetime',
            'jumlah_bayar' => 'decimal:2',
        ];
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
