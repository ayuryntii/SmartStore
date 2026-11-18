<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'peran',
        'status_aktif',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
            'kata_sandi' => 'hashed',
        ];
    }

    /**
     * Override getAuthPassword for custom column name 'kata_sandi'
     */
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function getAuthPasswordName()
    {
        return 'kata_sandi';
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->peran === 'kasir';
    }

    public function isGudang(): bool
    {
        return $this->peran === 'gudang';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->peran, $roles);
        }
        return $this->peran === $roles;
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'pengguna_id');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'pengguna_id');
    }

    public function pembayaranPenjualan()
    {
        return $this->hasMany(PembayaranPenjualan::class, 'pengguna_id');
    }

    public function rencanaBelanja()
    {
        return $this->hasMany(RencanaBelanja::class, 'pengguna_id');
    }

    public function riwayatAktivitas()
    {
        return $this->hasMany(RiwayatAktivitas::class, 'pengguna_id');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'pengguna_id');
    }
}
