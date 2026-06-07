-- schema.sql
-- Run this file in your MySQL database to create the ENTIRE database and tables.

CREATE DATABASE IF NOT EXISTS `ngolab_loyalty`;
USE `ngolab_loyalty`;

-- ==========================================
-- 1. Tabel Pengguna & Staff (Anggota 1)
-- ==========================================
CREATE TABLE IF NOT EXISTS `TABEL_USER` (
  `id_user` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `nim` VARCHAR(20) DEFAULT NULL,
  `saldo_poin` INT DEFAULT 10,
  `is_shared_sosmed` BOOLEAN DEFAULT FALSE,
  `api_key` VARCHAR(100) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `TABEL_STAFF` (
  `id_staff` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'kasir', 'superadmin') DEFAULT 'admin'
);

-- ==========================================
-- 2. Tabel Menu & Feedback (Anggota 2)
-- ==========================================
CREATE TABLE IF NOT EXISTS `TABEL_MENU` (
  `id_menu` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_menu` VARCHAR(150) NOT NULL,
  `harga` INT NOT NULL,
  `gambar_menu` VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `TABEL_FEEDBACK` (
  `id_feedback` INT AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `ulasan` TEXT,
  `bonus_poin_diklaim` BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (`id_user`) REFERENCES `TABEL_USER`(`id_user`) ON DELETE CASCADE
);

-- ==========================================
-- 3. Tabel Kategori & Reward (Anggota 2 & 3)
-- ==========================================
CREATE TABLE IF NOT EXISTS `TABEL_KATEGORI` (
  `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT
);

CREATE TABLE IF NOT EXISTS `TABEL_REWARD` (
  `id_reward` INT AUTO_INCREMENT PRIMARY KEY,
  `id_kategori` INT NOT NULL,
  `nama_reward` VARCHAR(150) NOT NULL,
  `poin_dibutuhkan` INT NOT NULL,
  `stok` INT NOT NULL DEFAULT 0,
  `gambar` VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (`id_kategori`) REFERENCES `TABEL_KATEGORI`(`id_kategori`) ON DELETE CASCADE
);

-- ==========================================
-- 4. Tabel Transaksi & Penukaran (Anggota 3)
-- ==========================================
CREATE TABLE IF NOT EXISTS `TABEL_PESANAN` (
  `id_pesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `id_menu` INT NOT NULL,
  `jumlah` INT NOT NULL,
  `total_harga` INT NOT NULL,
  `poin_didapat` INT NOT NULL,
  `catatan_pesanan` TEXT,
  `status` ENUM('pending', 'diproses', 'selesai') DEFAULT 'pending',
  `tanggal_pesan` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_user`) REFERENCES `TABEL_USER`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_menu`) REFERENCES `TABEL_MENU`(`id_menu`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `TABEL_PENUKARAN_REWARD` (
  `id_penukaran` INT AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `id_reward` INT NOT NULL,
  `tanggal_tukar` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'berhasil', 'ditolak') DEFAULT 'pending',
  FOREIGN KEY (`id_user`) REFERENCES `TABEL_USER`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_reward`) REFERENCES `TABEL_REWARD`(`id_reward`) ON DELETE CASCADE
);

-- ==========================================
-- DUMMY DATA INISIALISASI
-- ==========================================
-- Insert dummy superadmin for testing
INSERT INTO `TABEL_STAFF` (`username`, `email`, `password`, `role`) 
VALUES ('Super Admin', 'admin@ngolab.com', SHA2('admin123', 256), 'superadmin');
