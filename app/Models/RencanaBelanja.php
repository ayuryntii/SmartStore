<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaBelanja extends Model
{
    use HasFactory;

    protected $table = 'rencana_belanja';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'pengguna_id',
        'tanggal_rencana',
        'status_rencana',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_rencana' => 'date',
        ];
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function detailRencanaBelanja()
    {
        return $this->hasMany(DetailRencanaBelanja::class, 'rencana_belanja_id');
    }
}
