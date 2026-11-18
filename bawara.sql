-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2026 at 05:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bawara`
--
CREATE DATABASE IF NOT EXISTS `bawara` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bawara`;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE IF NOT EXISTS `barang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `satuan_id` bigint(20) UNSIGNED NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_beli` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stok_minimum` decimal(12,3) NOT NULL DEFAULT 0.000,
  `stok_saat_ini` decimal(12,3) NOT NULL DEFAULT 0.000,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batch_barang`
--

CREATE TABLE IF NOT EXISTS `batch_barang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `detail_pembelian_id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `kode_batch` varchar(100) DEFAULT NULL,
  `tanggal_kedaluwarsa` date DEFAULT NULL,
  `jumlah_diterima` decimal(12,3) NOT NULL,
  `sisa_jumlah` decimal(12,3) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

CREATE TABLE IF NOT EXISTS `detail_pembelian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pembelian_id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` decimal(12,3) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE IF NOT EXISTS `detail_penjualan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penjualan_id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` decimal(12,3) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `harga_pokok` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_rencana_belanja`
--

CREATE TABLE IF NOT EXISTS `detail_rencana_belanja` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rencana_belanja_id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah_dibutuhkan` decimal(12,3) NOT NULL,
  `jumlah_dibeli` decimal(12,3) NOT NULL DEFAULT 0.000,
  `status_item` enum('menunggu','dibeli','dibatalkan') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE IF NOT EXISTS `kategori_barang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_barang`
--

INSERT INTO `kategori_barang` (`id`, `nama_kategori`, `deskripsi`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 'Makanan', 'Produk makanan dan camilan', '2026-11-04 04:49:44', '2026-11-04 04:49:44'),
(2, 'Minuman', 'Produk minuman', '2026-11-04 04:49:44', '2026-11-04 04:49:44'),
(3, 'Sembako', 'Kebutuhan pokok sehari-hari', '2026-11-04 04:49:44', '2026-11-04 04:49:44'),
(4, 'Perlengkapan Rumah', 'Kebutuhan rumah tangga', '2026-11-04 04:49:44', '2026-11-04 04:49:44'),
(5, 'Perawatan Pribadi', 'Produk perawatan diri', '2026-11-04 04:49:44', '2026-11-04 04:49:44')
ON DUPLICATE KEY UPDATE `nama_kategori`=VALUES(`nama_kategori`);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pelanggan` varchar(150) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama_pelanggan`, `nomor_telepon`, `alamat`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 'Pelanggan Umum', NULL, NULL, '2026-11-04 04:49:44', '2026-11-04 04:49:44')
ON DUPLICATE KEY UPDATE `nama_pelanggan`=VALUES(`nama_pelanggan`);

-- --------------------------------------------------------

--
-- Table structure for table `pemasok`
--

CREATE TABLE IF NOT EXISTS `pemasok` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pemasok` varchar(150) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pemasok`
--

INSERT INTO `pemasok` (`id`, `nama_pemasok`, `nomor_telepon`, `alamat`, `email`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 'Toko Grosir Sejahtera', '081234567890', 'Cirebon', NULL, '2026-11-04 04:49:44', '2026-11-04 04:49:44'),
(2, 'Distributor Makmur', '081234567891', 'Cirebon', NULL, '2026-11-04 04:49:44', '2026-11-04 04:49:44')
ON DUPLICATE KEY UPDATE `nama_pemasok`=VALUES(`nama_pemasok`);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_penjualan`
--

CREATE TABLE IF NOT EXISTS `pembayaran_penjualan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penjualan_id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `metode_pembayaran` enum('tunai','transfer','qris','lainnya') NOT NULL DEFAULT 'tunai',
  `tanggal_pembayaran` datetime NOT NULL,
  `catatan` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE IF NOT EXISTS `pembelian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pemasok_id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_faktur` varchar(100) DEFAULT NULL,
  `tanggal_pembelian` datetime NOT NULL,
  `status_pembelian` enum('draf','diterima','dibatalkan') NOT NULL DEFAULT 'draf',
  `total_pembelian` decimal(15,2) NOT NULL DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `peran` enum('admin','kasir','gudang') NOT NULL DEFAULT 'admin',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE IF NOT EXISTS `penjualan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pelanggan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_faktur` varchar(100) NOT NULL,
  `tanggal_penjualan` datetime NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `total_harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `diskon` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_penjualan` enum('draf','selesai','dibatalkan') NOT NULL DEFAULT 'selesai',
  `catatan` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rencana_belanja`
--

CREATE TABLE IF NOT EXISTS `rencana_belanja` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_rencana` date NOT NULL,
  `status_rencana` enum('draf','dipesan','selesai','dibatalkan') NOT NULL DEFAULT 'draf',
  `catatan` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_aktivitas`
--

CREATE TABLE IF NOT EXISTS `riwayat_aktivitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `aksi` varchar(100) NOT NULL,
  `nama_tabel` varchar(100) DEFAULT NULL,
  `data_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `alamat_ip` varchar(45) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_stok`
--

CREATE TABLE IF NOT EXISTS `riwayat_stok` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_pergerakan` enum('pembelian_masuk','penjualan_keluar','penyesuaian_masuk','penyesuaian_keluar','retur_masuk','retur_keluar') NOT NULL,
  `jumlah` decimal(12,3) NOT NULL,
  `jenis_referensi` varchar(50) DEFAULT NULL,
  `referensi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal_pergerakan` datetime NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_stok_habis`
--

CREATE TABLE IF NOT EXISTS `riwayat_stok_habis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `mulai_habis_pada` datetime NOT NULL,
  `berakhir_pada` datetime DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE IF NOT EXISTS `satuan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_satuan` varchar(50) NOT NULL,
  `singkatan` varchar(15) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id`, `nama_satuan`, `singkatan`, `dibuat_pada`) VALUES
(1, 'Pcs', 'pcs', '2026-11-04 04:49:44'),
(2, 'Bungkus', 'bks', '2026-11-04 04:49:44'),
(3, 'Dus', 'dus', '2026-11-04 04:49:44'),
(4, 'Kilogram', 'kg', '2026-11-04 04:49:44'),
(5, 'Liter', 'L', '2026-11-04 04:49:44'),
(6, 'Botol', 'btl', '2026-11-04 04:49:44'),
(7, 'Pack', 'pack', '2026-11-04 04:49:44')
ON DUPLICATE KEY UPDATE `nama_satuan`=VALUES(`nama_satuan`);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `kode_barang` (`kode_barang`),
  ADD KEY IF NOT EXISTS `fk_barang_kategori` (`kategori_id`),
  ADD KEY IF NOT EXISTS `fk_barang_satuan` (`satuan_id`),
  ADD KEY IF NOT EXISTS `idx_barang_nama` (`nama_barang`),
  ADD KEY IF NOT EXISTS `idx_barang_stok` (`stok_saat_ini`);

--
-- Indexes for table `batch_barang`
--
ALTER TABLE `batch_barang`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_batch_detail_pembelian` (`detail_pembelian_id`),
  ADD KEY IF NOT EXISTS `fk_batch_barang` (`barang_id`),
  ADD KEY IF NOT EXISTS `idx_batch_kedaluwarsa` (`tanggal_kedaluwarsa`);

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_detail_pembelian` (`pembelian_id`),
  ADD KEY IF NOT EXISTS `idx_detail_pembelian_barang` (`barang_id`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_detail_penjualan` (`penjualan_id`),
  ADD KEY IF NOT EXISTS `idx_detail_penjualan_barang` (`barang_id`);

--
-- Indexes for table `detail_rencana_belanja`
--
ALTER TABLE `detail_rencana_belanja`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_detail_rencana` (`rencana_belanja_id`),
  ADD KEY IF NOT EXISTS `fk_detail_rencana_barang` (`barang_id`);

--
-- Indexes for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY IF NOT EXISTS (`id`);

--
-- Indexes for table `pemasok`
--
ALTER TABLE `pemasok`
  ADD PRIMARY KEY IF NOT EXISTS (`id`);

--
-- Indexes for table `pembayaran_penjualan`
--
ALTER TABLE `pembayaran_penjualan`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_pembayaran_penjualan` (`penjualan_id`),
  ADD KEY IF NOT EXISTS `fk_pembayaran_pengguna` (`pengguna_id`),
  ADD KEY IF NOT EXISTS `idx_pembayaran_tanggal` (`tanggal_pembayaran`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `nomor_faktur` (`nomor_faktur`),
  ADD KEY IF NOT EXISTS `fk_pembelian_pemasok` (`pemasok_id`),
  ADD KEY IF NOT EXISTS `fk_pembelian_pengguna` (`pengguna_id`),
  ADD KEY IF NOT EXISTS `idx_pembelian_tanggal` (`tanggal_pembelian`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `email` (`email`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `nomor_faktur` (`nomor_faktur`),
  ADD KEY IF NOT EXISTS `fk_penjualan_pelanggan` (`pelanggan_id`),
  ADD KEY IF NOT EXISTS `fk_penjualan_pengguna` (`pengguna_id`),
  ADD KEY IF NOT EXISTS `idx_penjualan_tanggal` (`tanggal_penjualan`),
  ADD KEY IF NOT EXISTS `idx_penjualan_status` (`status_penjualan`);

--
-- Indexes for table `rencana_belanja`
--
ALTER TABLE `rencana_belanja`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_rencana_pengguna` (`pengguna_id`);

--
-- Indexes for table `riwayat_aktivitas`
--
ALTER TABLE `riwayat_aktivitas`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_aktivitas_pengguna` (`pengguna_id`),
  ADD KEY IF NOT EXISTS `idx_aktivitas_tanggal` (`dibuat_pada`);

--
-- Indexes for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `fk_riwayat_stok_batch` (`batch_id`),
  ADD KEY IF NOT EXISTS `fk_riwayat_stok_pengguna` (`pengguna_id`),
  ADD KEY IF NOT EXISTS `idx_riwayat_barang_tanggal` (`barang_id`,`tanggal_pergerakan`);

--
-- Indexes for table `riwayat_stok_habis`
--
ALTER TABLE `riwayat_stok_habis`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `idx_stok_habis_barang` (`barang_id`,`mulai_habis_pada`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD UNIQUE KEY IF NOT EXISTS `nama_satuan` (`nama_satuan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `batch_barang`
--
ALTER TABLE `batch_barang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_rencana_belanja`
--
ALTER TABLE `detail_rencana_belanja`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pemasok`
--
ALTER TABLE `pemasok`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pembayaran_penjualan`
--
ALTER TABLE `pembayaran_penjualan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rencana_belanja`
--
ALTER TABLE `rencana_belanja`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_aktivitas`
--
ALTER TABLE `riwayat_aktivitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_stok_habis`
--
ALTER TABLE `riwayat_stok_habis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_barang` (`id`),
  ADD CONSTRAINT `fk_barang_satuan` FOREIGN KEY (`satuan_id`) REFERENCES `satuan` (`id`);

--
-- Constraints for table `batch_barang`
--
ALTER TABLE `batch_barang`
  ADD CONSTRAINT `fk_batch_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`),
  ADD CONSTRAINT `fk_batch_detail_pembelian` FOREIGN KEY (`detail_pembelian_id`) REFERENCES `detail_pembelian` (`id`);

--
-- Constraints for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `fk_detail_pembelian` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_pembelian_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `fk_detail_penjualan` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_penjualan_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);

--
-- Constraints for table `detail_rencana_belanja`
--
ALTER TABLE `detail_rencana_belanja`
  ADD CONSTRAINT `fk_detail_rencana` FOREIGN KEY (`rencana_belanja_id`) REFERENCES `rencana_belanja` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_rencana_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);

--
-- Constraints for table `pembayaran_penjualan`
--
ALTER TABLE `pembayaran_penjualan`
  ADD CONSTRAINT `fk_pembayaran_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `fk_pembayaran_penjualan` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`);

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `fk_pembelian_pemasok` FOREIGN KEY (`pemasok_id`) REFERENCES `pemasok` (`id`),
  ADD CONSTRAINT `fk_pembelian_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`);

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `fk_penjualan_pelanggan` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_penjualan_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`);

--
-- Constraints for table `rencana_belanja`
--
ALTER TABLE `rencana_belanja`
  ADD CONSTRAINT `fk_rencana_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`);

--
-- Constraints for table `riwayat_aktivitas`
--
ALTER TABLE `riwayat_aktivitas`
  ADD CONSTRAINT `fk_aktivitas_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD CONSTRAINT `fk_riwayat_stok_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`),
  ADD CONSTRAINT `fk_riwayat_stok_batch` FOREIGN KEY (`batch_id`) REFERENCES `batch_barang` (`id`),
  ADD CONSTRAINT `fk_riwayat_stok_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`);

--
-- Constraints for table `riwayat_stok_habis`
--
ALTER TABLE `riwayat_stok_habis`
  ADD CONSTRAINT `fk_stok_habis_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
