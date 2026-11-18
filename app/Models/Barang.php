<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kategori_id',
        'satuan_id',
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'harga_beli',
        'harga_jual',
        'stok_minimum',
        'stok_saat_ini',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'stok_minimum' => 'decimal:3',
            'stok_saat_ini' => 'decimal:3',
            'status_aktif' => 'boolean',
        ];
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function batchBarang()
    {
        return $this->hasMany(BatchBarang::class, 'barang_id');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'barang_id');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'barang_id');
    }

    public function detailRencanaBelanja()
    {
        return $this->hasMany(DetailRencanaBelanja::class, 'barang_id');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'barang_id')->orderBy('tanggal_pergerakan', 'desc');
    }

    public function riwayatStokHabis()
    {
        return $this->hasMany(RiwayatStokHabis::class, 'barang_id')->orderBy('mulai_habis_pada', 'desc');
    }

    /**
     * Status stok: aman, menipis, habis
     */
    public function getStatusStokAttribute(): string
    {
        if ((float) $this->stok_saat_ini <= 0) {
            return 'habis';
        }
        if ((float) $this->stok_saat_ini <= (float) $this->stok_minimum) {
            return 'menipis';
        }
        return 'aman';
    }

    public function getStatusStokLabelAttribute(): string
    {
        return match ($this->status_stok) {
            'habis' => 'Habis',
            'menipis' => 'Menipis',
            default => 'Aman',
        };
    }
}
