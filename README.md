<div align="center">

# 🏪 RetailCore
### *Enterprise-grade Web-based Retail Point of Sale (POS) & Multi-Batch FEFO Inventory Platform*

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL / MariaDB](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Chart.js](https://img.shields.io/badge/Analytics-Chart.js%204.x-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)](https://chartjs.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

<br/>

**Developed by Ayu Rianti • Universitas Muhammadiyah Cirebon (UMC)**

</div>

---

## 📌 Executive Summary

**RetailCore** adalah platform sistem manajemen operasional dan *Point of Sale* (POS) ritel modern berbasis web yang dirancang khusus untuk mentransformasi tata kelola warung dan toko sembako tradisional menjadi ekosistem digital yang efisien, transparan, dan akurat.

Sistem ini memecahkan berbagai tantangan krusial operasional ritel: pencatatan transaksi manual yang rentan selisih, kerugian akibat barang kedaluwarsa tanpa pelacakan batch, piutang pelanggan (kasbon/utang warung) yang tercecer, serta pembelian restok yang tidak terukur. Dilengkapi arsitektur modern Laravel 11, pelacakan inventaris **FEFO (*First Expired, First Out*)**, integrasi **Moving Average Demand Forecasting**, dan modul pencatatan **Buku Utang Pelanggan**, RetailCore menghadirkan solusi operasional *end-to-end* yang ramah pengguna.

---

## 🚀 Key Engineering & Business Features

### 1. 🛒 Dual-Mode Smart Point of Sale (POS)
- **High-Velocity Checkout**: Pencarian instan katalog barang sembako berdasarkan nama dan kode barang (SKU).
- **Dual Payment Workflows**:
  - **Transaksi Tunai (Cash)**: Kalkulasi subtotal, diskon, dan uang kembalian secara otomatis dan akurat.
  - **Transaksi Utang (Customer Credit / Kasbon)**: Terintegrasi langsung dengan master pelanggan, batas jatuh tempo (tempo limit), dan penerbitan faktur piutang digital.
- **Struk Digital & Ringkasan Transaksi**: Bukti transaksi tercatat otomatis ke dalam mutasi kas fisik dan omzet harian.

### 2. 📦 Multi-Batch Inventory Tracking & FEFO Engine
- **First Expired, First Out (FEFO)**: Setiap barang masuk (*Purchase Order*) tercatat ke dalam tabel `batch_barang` dengan nomor batch unik dan tanggal kedaluwarsa.
- **Automated Expiry Depletion**: Pengurangan stok saat kasir memproses penjualan secara otomatis memprioritaskan stok dari batch yang memiliki tanggal kedaluwarsa terdekat.
- **Visual Expiry & Threshold Indicators**: Indikator status stok (*Aman, Menipis, Habis*) dan status kedaluwarsa (*Aman, Mendekati Kadaluarsa, Kadaluarsa*) di seluruh modul katalog.

### 3. 📒 Customer Credit Ledger (Buku Utang Digital)
- **Comprehensive Debt Monitoring**: Pelacakan total saldo piutang pelanggan aktif secara *real-time*.
- **Due Date Tracker**: Pengingat jatuh tempo dengan kategorisasi otomatis (*Belum Jatuh Tempo, Mendekati Jatuh Tempo, Terlambat*).
- **Catat Cicilan & Pelunasan Fleksibel**: Riwayat pembayaran bertahap (angsuran/cicilan) hingga lunas dengan riwayat audit lengkap.

### 4. 📈 Predictive Restock with Moving Average
- **Intelligent Stock Replenishment**: Algoritma perkiraan kebutuhan barang berbasis rata-rata bergerak (*Moving Average*) dari riwayat penjualan berkala.
- **Optimal Order Recommendation**: Mencegah *overstock* (modal tertahan) maupun *stockout* (kehabisan barang sembako vital).

### 5. 📊 Interactive Analytics & Real-Time Dashboards
- **Daily & Monthly Revenue Curves**: Visualisasi kurva pertumbuhan omzet harian dan bulanan menggunakan Chart.js.
- **Cash Flow Reconciliation**: Pemisahan jelas antara total omzet, uang fisik kasir masuk (*real cash*), dan tagihan piutang berjalan.
- **High-Converting Soft UI Palette**: Antarmuka responsif dengan skema warna *earthy sage & ivory cream* yang dirancang nyaman dipandang (*ergonomic modern UX*).

### 6. 🔐 Multi-Role Access Control (RBAC)
- **Super Admin**: Akses penuh ke master data produk, manajemen pemasok (*supplier*), pengadaan restok, analisis moving average, pembukuan keuangan, dan manajemen akun staf.
- **Kasir Operational**: Akses cepat ke halaman POS penjualan kasir, pengecekan ketersediaan fisik stok barang di rak, pencatatan transaksi utang, dan pembayaran cicilan pelanggan.

---

## 🛠️ Technology Stack

| Layer | Technology | Description |
| :--- | :--- | :--- |
| **Backend Framework** | **Laravel 11.x** | Modern MVC Architecture, Eloquent ORM, Service Layer |
| **Language** | **PHP 8.2+** | Strictly typed, robust enterprise backend runtime |
| **Frontend UI** | **Blade Templating & Tailwind CSS 3.4** | Utility-first, responsive, and custom soft theme |
| **Data Visualization** | **Chart.js 4.x** | Interactive financial & transaction charts |
| **Database** | **MySQL 8.0 / MariaDB 10.4+** | Relational schema with Foreign Keys & Transaction ACID |
| **Authentication** | **Laravel Session & Role Middleware** | Secure RBAC with Bcrypt hashing |
| **Web Server** | **Apache / Nginx / Artisan CLI** | Multi-environment deployment readiness |

---

## 🗄️ Database Architecture Highlights

Sistem dibangun di atas skema relasional teroptimasi:
- `users`: Manajemen akun Admin dan Kasir berbasis *role*.
- `kategori`: Klasifikasi produk sembako (*Sembako Utama, Makanan Instan, Minuman, Kebersihan, Bumbu*).
- `barang`: Master data produk, SKU, barcode, stok minimal, harga beli, dan harga jual.
- `batch_barang`: Pelacakan stok multi-batch, kode batch, tanggal kedaluwarsa, dan kuantitas tersisa.
- `penjualan` & `detail_penjualan`: Pencatatan header dan item penjualan kasir.
- `pelanggan`: Direktori pelanggan warung beserta nomor telepon dan alamat.
- `utang` & `pembayaran_utang`: Buku besar piutang pelanggan, status tempo, dan log angsuran.
- `pemasok`, `pembelian`, & `detail_pembelian`: Alur pengadaan stok dari distributor/grosir.
- `riwayat_stok`: Audit log otomatis mutasi keluar-masuk stok barang.

---

## 💻 Getting Started / Installation Guide

Ikuti panduan berikut untuk menjalankan proyek di lingkungan lokal:

### 1. Prasyarat Sistem
- PHP >= 8.2 (ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL / MariaDB Server (misal via XAMPP)

### 2. Kloning Repositori
```bash
git clone https://github.com/ayuryntii/RetailCore.git
cd RetailCore
```

### 3. Instalasi Dependensi Backend & Frontend
```bash
# Instal dependensi PHP
composer install

# Instal dependensi Node.js & bangun asset frontend
npm install
npm run build
```

### 4. Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env` lalu sesuaikan kredensial basis data Anda:
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan konfigurasi database di `.env` sudah sesuai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bawara
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeding Data
Buat database bernama `bawara` di MySQL / phpMyAdmin, lalu jalankan migrasi dan seeder awal:
```bash
php artisan migrate:fresh --seed
```
*(Alternatif: Anda dapat mengimpor berkas `bawara.sql` langsung melalui phpMyAdmin)*

### 6. Jalankan Server Pengembangan
```bash
php artisan serve
```
Aplikasi kini dapat diakses melalui peramban web di: **`http://127.0.0.1:8000`**

---

## 🔑 Demo Credentials

| Role | Email Login | Password | Akses Utama |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@bawara.id` | `password123` | Dashboard Analytics, Master Barang, Pembelian, Moving Average, Laporan |
| **Kasir** | `kasir@bawara.id` | `password123` | POS Kasir, Pencarian Info Barang, Transaksi Utang, Catat Cicilan |

---

## 👩‍💻 Author & Developer

Developed and maintained by:
- **Ayu Rianti** ([@ayuryntii](https://github.com/ayuryntii)) — *Lead Software Engineer & Full-Stack Developer*
- Program Studi Teknik Informatika, Fakultas Teknik, **Universitas Muhammadiyah Cirebon (UMC)**

---

## 📄 Lisensi

Didistribusikan di bawah lisensi terbuka [MIT License](LICENSE). Bebas digunakan, dipelajari, dan dikembangkan untuk tujuan edukasi dan peningkatan ekosistem ritel & UMKM.
