<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'jumlah',
        'harga_satuan',
        'harga_pokok',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:3',
            'harga_satuan' => 'decimal:2',
            'harga_pokok' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
