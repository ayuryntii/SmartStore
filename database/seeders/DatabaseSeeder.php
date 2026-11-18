<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        Pengguna::firstOrCreate(
            ['email' => 'admin@warungku.id'],
            [
                'nama' => 'Administrator Warungku',
                'kata_sandi' => Hash::make('password123'),
                'peran' => 'admin',
                'status_aktif' => 1,
            ]
        );

        // 2. Kasir
        Pengguna::firstOrCreate(
            ['email' => 'kasir@warungku.id'],
            [
                'nama' => 'Kasir Warungku',
                'kata_sandi' => Hash::make('password123'),
                'peran' => 'kasir',
                'status_aktif' => 1,
            ]
        );

        // 3. Gudang
        Pengguna::firstOrCreate(
            ['email' => 'gudang@warungku.id'],
            [
                'nama' => 'Petugas Gudang',
                'kata_sandi' => Hash::make('password123'),
                'peran' => 'gudang',
                'status_aktif' => 1,
            ]
        );
    }
}
