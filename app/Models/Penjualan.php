<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'pelanggan_id',
        'pengguna_id',
        'nomor_faktur',
        'tanggal_penjualan',
        'tanggal_jatuh_tempo',
        'total_harga',
        'diskon',
        'total_bayar',
        'status_penjualan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penjualan' => 'datetime',
            'tanggal_jatuh_tempo' => 'date',
            'total_harga' => 'decimal:2',
            'diskon' => 'decimal:2',
            'total_bayar' => 'decimal:2',
        ];
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function pembayaranPenjualan()
    {
        return $this->hasMany(PembayaranPenjualan::class, 'penjualan_id')->orderBy('tanggal_pembayaran', 'desc');
    }

    /**
     * Total tagihan = total_harga - diskon
     */
    public function getTotalTagihanAttribute(): float
    {
        return max(0, (float) $this->total_harga - (float) $this->diskon);
    }

    /**
     * Sisa tagihan / utang
     */
    public function getSisaTagihanAttribute(): float
    {
        return max(0, $this->total_tagihan - (float) $this->total_bayar);
    }

    /**
     * Status pembayaran turunan (belum dibayar, dibayar sebagian, lunas, jatuh tempo)
     */
    public function getStatusPembayaranAttribute(): string
    {
        if ($this->status_penjualan === 'dibatalkan') {
            return 'dibatalkan';
        }

        if ($this->sisa_tagihan <= 0.001) {
            return 'lunas';
        }

        $isJatuhTempo = $this->tanggal_jatuh_tempo && now()->startOfDay()->gt($this->tanggal_jatuh_tempo->startOfDay());

        if ($isJatuhTempo) {
            return 'jatuh_tempo';
        }

        if ((float) $this->total_bayar > 0) {
            return 'dibayar_sebagian';
        }

        return 'belum_dibayar';
    }

    public function getStatusPembayaranLabelAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'lunas' => 'Lunas',
            'dibayar_sebagian' => 'Dibayar Sebagian',
            'belum_dibayar' => 'Belum Dibayar',
            'jatuh_tempo' => 'Jatuh Tempo',
            'dibatalkan' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }
}
