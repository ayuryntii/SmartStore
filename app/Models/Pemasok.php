<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    use HasFactory;

    protected $table = 'pemasok';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_pemasok',
        'nomor_telepon',
        'alamat',
        'email',
    ];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'pemasok_id');
    }
}
