<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchBarang extends Model
{
    use HasFactory;

    protected $table = 'batch_barang';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'detail_pembelian_id',
        'barang_id',
        'kode_batch',
        'tanggal_kedaluwarsa',
        'jumlah_diterima',
        'sisa_jumlah',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kedaluwarsa' => 'date',
            'jumlah_diterima' => 'decimal:3',
            'sisa_jumlah' => 'decimal:3',
        ];
    }

    public function detailPembelian()
    {
        return $this->belongsTo(DetailPembelian::class, 'detail_pembelian_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'batch_id');
    }

    /**
     * Expiry status helper
     */
    public function getStatusKedaluwarsaAttribute(): string
    {
        if (!$this->tanggal_kedaluwarsa) {
            return 'tidak_ada';
        }

        $now = now()->startOfDay();
        $tgl = $this->tanggal_kedaluwarsa->copy()->startOfDay();

        if ($tgl->lt($now)) {
            return 'kedaluwarsa';
        }

        $days = (int) $now->diffInDays($tgl, false);

        if ($days <= 7) {
            return 'kritis'; // <= 7 hari
        }
        if ($days <= 30) {
            return 'peringatan'; // <= 30 hari
        }

        return 'aman';
    }
}
