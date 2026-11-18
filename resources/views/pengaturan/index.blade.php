@extends('layouts.app')

@section('title', 'Pengaturan & Informasi Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="space-y-6 max-w-5xl">
    <!-- About BAWARA App -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo BAWARA" class="w-16 h-16 rounded-2xl object-contain shadow-xs border border-gray-100 p-1">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">BAWARA</h2>
                    <p class="text-xs text-gray-500 font-medium">Sistem Informasi Manajemen Persediaan dan Operasional Warung Berbasis Web</p>
                    <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Capstone Project Teknik Informatika - Versi 1.0.0 (Production Ready)
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('pengaturan.backup') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-xs transition-colors">
                    <i data-lucide="database-backup" class="w-4 h-4"></i>
                    Unduh Cadangan SQL (bawara.sql)
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/60">
                <div class="text-xs text-gray-500 font-medium">Versi PHP</div>
                <div class="text-base font-bold text-gray-900 mt-1 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    PHP {{ $phpVersion }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/60">
                <div class="text-xs text-gray-500 font-medium">Framework</div>
                <div class="text-base font-bold text-gray-900 mt-1 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                    Laravel {{ $laravelVersion }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/60">
                <div class="text-xs text-gray-500 font-medium">Database Server</div>
                <div class="text-base font-bold text-gray-900 mt-1 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    {{ $mariaDbVersion }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/60">
                <div class="text-xs text-gray-500 font-medium">Nama Database</div>
                <div class="text-base font-bold text-gray-900 mt-1 flex items-center gap-2 font-mono">
                    <i data-lucide="database" class="w-4 h-4 text-emerald-600"></i>
                    {{ $dbName }} ({{ $dbHost }})
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Specifications Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-3">
                <i data-lucide="cpu" class="w-4 h-4 text-emerald-600"></i>
                Fitur Utama Sistem BAWARA
            </h3>
            <ul class="text-xs text-gray-600 space-y-2.5">
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                    <span><strong>Deteksi Barang Habis Otomatis:</strong> Menggunakan 3 pilar: pengurangan otomatis penjualan, manual penyesuaian, dan Tombol Cepat Lapor Habis.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                    <span><strong>Metode FEFO (First Expired First Out):</strong> Pengeluaran batch stok otomatis memprioritaskan tanggal kedaluwarsa terdekat.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                    <span><strong>Peramalan Kebutuhan Stok:</strong> Moving Average harian untuk estimasi kebutuhan restock dan rekomendasi belanja.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                    <span><strong>Manajemen Piutang Pelanggan:</strong> Pencatatan faktur utang, jatuh tempo, cicilan parsial, hingga pelunasan multi-faktur.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                    <span><strong>Otorisasi Berlapis (RBAC):</strong> Admin, Kasir, dan Gudang terisolasi secara ketat pada level middleware backend.</span>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-3">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                Integritas Database (17 Tabel bawara.sql)
            </h3>
            <div class="grid grid-cols-2 gap-2 text-[11px] font-mono text-gray-600">
                <div class="p-2 bg-gray-50 rounded border border-gray-100">barang</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">batch_barang</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">detail_pembelian</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">detail_penjualan</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">detail_rencana_belanja</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">kategori_barang</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">pelanggan</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">pemasok</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">pembayaran_penjualan</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">pembelian</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">pengguna</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">penjualan</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">rencana_belanja</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">riwayat_aktivitas</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">riwayat_stok</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100">riwayat_stok_habis</div>
                <div class="p-2 bg-gray-50 rounded border border-gray-100 col-span-2">satuan</div>
            </div>
        </div>
    </div>
</div>
@endsection
