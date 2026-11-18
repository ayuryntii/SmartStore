<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStokHabis extends Model
{
    use HasFactory;

    protected $table = 'riwayat_stok_habis';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'barang_id',
        'mulai_habis_pada',
        'berakhir_pada',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'mulai_habis_pada' => 'datetime',
            'berakhir_pada' => 'datetime',
        ];
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Durasi habis dalam jam / hari
     */
    public function getDurasiHabisAttribute(): string
    {
        $start = $this->mulai_habis_pada;
        $end = $this->berakhir_pada ?? now();

        $diffHours = $start->diffInHours($end);
        if ($diffHours < 24) {
            return $diffHours . ' Jam';
        }
        $diffDays = $start->diffInDays($end);
        return $diffDays . ' Hari';
    }

    public function isMasihHabis(): bool
    {
        return is_null($this->berakhir_pada);
    }
}
