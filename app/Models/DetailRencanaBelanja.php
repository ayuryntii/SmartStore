<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailRencanaBelanja extends Model
{
    use HasFactory;

    protected $table = 'detail_rencana_belanja';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'rencana_belanja_id',
        'barang_id',
        'jumlah_dibutuhkan',
        'jumlah_dibeli',
        'status_item',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_dibutuhkan' => 'decimal:3',
            'jumlah_dibeli' => 'decimal:3',
        ];
    }

    public function rencanaBelanja()
    {
        return $this->belongsTo(RencanaBelanja::class, 'rencana_belanja_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
