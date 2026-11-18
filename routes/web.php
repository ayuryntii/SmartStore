<?php

use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\RencanaBelanjaController;
use App\Http\Controllers\RiwayatAktivitasController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StokController;
use Illuminate\Support\Facades\Route;

// Landing page utama sebelum login
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MODULE: Bersama (Admin & Kasir) - Sesuai Tabel 2.3 Matriks Hak Akses
    Route::middleware('role:admin,kasir')->group(function () {
        // Informasi Barang (Kasir hanya melihat informasi barang, Admin dapat melihat & mengelola)
        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');

        // Transaksi Penjualan
        Route::resource('penjualan', PenjualanController::class)->except(['edit', 'update', 'destroy']);
        Route::get('/penjualan/{penjualan}/cetak', [PenjualanController::class, 'printReceipt'])->name('penjualan.print');
        Route::post('/penjualan/{penjualan}/batal', [PenjualanController::class, 'cancel'])->name('penjualan.cancel');

        // Pelanggan
        Route::resource('pelanggan', PelangganController::class);

        // Piutang Pelanggan & Pembayaran Utang
        Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
        Route::get('/piutang/{pelanggan}', [PiutangController::class, 'show'])->name('piutang.show');
        Route::post('/piutang/bayar', [PiutangController::class, 'bayar'])->name('piutang.bayar');
        Route::post('/piutang/pelanggan/{pelanggan}/bayar', [PiutangController::class, 'bayarPelanggan'])->name('piutang.bayar-pelanggan');
    });

    // MODULE: Khusus Admin (Tabel 2.3: Kelola Barang, Stok, Pembelian, Pemasok, Rencana Belanja, Analisis, Laporan, Pengguna)
    Route::middleware('role:admin')->group(function () {
        // Kelola Barang
        Route::get('/barang/tambah/baru', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::post('/barang/{barang}/lapor-habis', [BarangController::class, 'reportOutOfStock'])->name('barang.lapor-habis');

        // Kategori & Satuan
        Route::resource('kategori', KategoriController::class)->except(['create', 'show', 'edit']);
        Route::resource('satuan', SatuanController::class)->except(['create', 'show', 'edit']);

        // Manajemen Stok (Stok masuk, stok keluar, penyesuaian stok)
        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/stok/riwayat', [StokController::class, 'riwayat'])->name('stok.riwayat');
        Route::get('/stok/penyesuaian', [StokController::class, 'penyesuaianForm'])->name('stok.penyesuaian');
        Route::post('/stok/penyesuaian', [StokController::class, 'penyesuaianSubmit'])->name('stok.penyesuaian.submit');

        // Pembelian & Penerimaan Barang
        Route::resource('pembelian', PembelianController::class)->except(['edit', 'update', 'destroy']);
        Route::post('/pembelian/{pembelian}/terima', [PembelianController::class, 'receive'])->name('pembelian.receive');

        // Pemasok
        Route::resource('pemasok', PemasokController::class);

        // Rencana Belanja (Prediksi Moving Average & Otomatisasi Belanja)
        Route::resource('rencana-belanja', RencanaBelanjaController::class)->except(['edit', 'update', 'destroy']);
        Route::patch('/rencana-belanja/item/{detailId}', [RencanaBelanjaController::class, 'updateItem'])->name('rencana-belanja.update-item');
        Route::patch('/rencana-belanja/{rencanaBelanja}/status', [RencanaBelanjaController::class, 'updateStatus'])->name('rencana-belanja.update-status');

        // Analisis Bisnis & Pantauan Kedaluwarsa
        Route::get('/analisis/terlaris', [AnalisisController::class, 'terlaris'])->name('analisis.terlaris');
        Route::get('/analisis/kedaluwarsa', [AnalisisController::class, 'kedaluwarsa'])->name('analisis.kedaluwarsa');
        Route::get('/analisis/sering-habis', [AnalisisController::class, 'seringHabis'])->name('analisis.sering-habis');
        Route::get('/analisis/prediksi', [AnalisisController::class, 'prediksi'])->name('analisis.prediksi');

        // Keuangan & Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');

        // Kelola Pengguna, Audit Trail & Pengaturan Sistem
        Route::resource('pengguna', PenggunaController::class)->except(['show']);
        Route::get('/aktivitas', [RiwayatAktivitasController::class, 'index'])->name('aktivitas.index');
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::get('/pengaturan/backup', [PengaturanController::class, 'downloadBackup'])->name('pengaturan.backup');
    });
});
