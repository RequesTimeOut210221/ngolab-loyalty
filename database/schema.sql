CREATE DATABASE IF NOT EXISTS `ngolab_loyalty`;
USE `ngolab_loyalty`;

CREATE TABLE IF NOT EXISTS `TABEL_MEMBER` (
  `id_member` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL UNIQUE,
  `nim` VARCHAR(20) DEFAULT NULL,
  `saldo_poin` INT NOT NULL DEFAULT 10,
  `is_shared_sosmed` BOOLEAN NOT NULL DEFAULT FALSE,
  `api_key` VARCHAR(100) DEFAULT NULL UNIQUE,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `TABEL_STAFF` (
  `id_staff` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'kasir', 'superadmin') NOT NULL DEFAULT 'admin',
  `no_hp` VARCHAR(20) DEFAULT NULL UNIQUE,
  `nim` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `TABEL_KATEGORI` (
  `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `TABEL_MENU` (
  `id_menu` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_menu` VARCHAR(150) NOT NULL,
  `harga` INT NOT NULL,
  `kategori` VARCHAR(50) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `poin_didapat` INT NOT NULL DEFAULT 0,
  `is_promo` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `TABEL_FEEDBACK` (
  `id_feedback` INT AUTO_INCREMENT PRIMARY KEY,
  `id_member` INT DEFAULT NULL,
  `nama_user` VARCHAR(100) DEFAULT 'Anonim',
  `rating` TINYINT NOT NULL DEFAULT 5,
  `ulasan` TEXT NOT NULL,
  `bonus_poin_diklaim` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_member`) REFERENCES `TABEL_MEMBER`(`id_member`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `TABEL_REWARD` (
  `id_reward` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_reward` VARCHAR(150) NOT NULL,
  `poin_dibutuhkan` INT NOT NULL,
  `stok` INT NOT NULL DEFAULT 0,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `kategori` VARCHAR(50) DEFAULT NULL,
  `tier` ENUM('basic', 'silver', 'gold') DEFAULT 'basic',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `TABEL_PESANAN` (
  `id_pesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_member` INT NOT NULL,
  `id_menu` INT NOT NULL,
  `jumlah` INT NOT NULL,
  `total_harga` INT NOT NULL,
  `poin_didapat` INT NOT NULL,
  `catatan_pesanan` TEXT,
  `status` ENUM('pending', 'diproses', 'selesai') NOT NULL DEFAULT 'pending',
  `tanggal_pesan` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_member`) REFERENCES `TABEL_MEMBER`(`id_member`) ON DELETE CASCADE,
  FOREIGN KEY (`id_menu`) REFERENCES `TABEL_MENU`(`id_menu`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `TABEL_PENUKARAN_REWARD` (
  `id_penukaran` INT AUTO_INCREMENT PRIMARY KEY,
  `id_member` INT NOT NULL,
  `id_reward` INT NOT NULL,
  `tanggal_tukar` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'berhasil', 'ditolak') NOT NULL DEFAULT 'pending',
  `token_wifi` VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (`id_member`) REFERENCES `TABEL_MEMBER`(`id_member`) ON DELETE CASCADE,
  FOREIGN KEY (`id_reward`) REFERENCES `TABEL_REWARD`(`id_reward`) ON DELETE CASCADE
);

INSERT INTO `TABEL_KATEGORI` (`nama_kategori`, `deskripsi`) VALUES
('cafe', 'Berbagai varian kopi, non-kopi, dan menu cafe'),
('bakso', 'Menu bakso dan makanan hangat')
ON DUPLICATE KEY UPDATE `deskripsi` = VALUES(`deskripsi`);

INSERT INTO `TABEL_MENU` (`nama_menu`, `harga`, `kategori`, `deskripsi`, `gambar`, `poin_didapat`) VALUES
('Kopi Susu Gula Aren', 15000, 'cafe', 'Espresso blend khas Ngolab dicampur susu segar dan sirup aren organik.', NULL, 1),
('Classic Espresso', 10000, 'cafe', 'Ekstraksi kopi murni konsentrat tinggi dari biji kopi arabika robusta pilihan.', NULL, 1),
('Caramel Macchiato', 22000, 'cafe', 'Espresso dengan steamed milk lembut dan karamel saus melimpah.', NULL, 2),
('Ice Americano', 12000, 'cafe', 'Espresso shot dingin dengan air jernih segar, pilihan terbaik penahan kantuk.', NULL, 1),
('Matcha Latte Green Tea', 20000, 'cafe', 'Bubuk matcha Uji Jepang premium diseduh dengan susu segar hangat/dingin.', NULL, 2),
('Bakso Mas Yanto Spesial Urat', 22000, 'bakso', 'Bakso urat jumbo berdaging tebal disajikan dengan kuah kaldu sapi pekat, mie, dan seledri.', NULL, 2),
('Bakso Halus Kuah Kaldu', 18000, 'bakso', '5 butir bakso halus lembut yang memanjakan lidah bersama kaldu sapi segar.', NULL, 1),
('Bakso Telur Rebus', 20000, 'bakso', 'Bakso daging sapi isi telur rebus utuh disiram kaldu panas gurih.', NULL, 2)
ON DUPLICATE KEY UPDATE `harga` = VALUES(`harga`);

INSERT INTO `TABEL_REWARD` (`nama_reward`, `poin_dibutuhkan`, `stok`, `gambar`) VALUES
('WiFi VIP Voucher 24 Jam', 5, 99, NULL),
('Kopi Susu Aren Gratis', 15, 12, NULL),
('Bakso Urat Jumbo Gratis', 25, 8, NULL)
ON DUPLICATE KEY UPDATE `stok` = VALUES(`stok`);

INSERT INTO `TABEL_STAFF` (`username`, `email`, `password`, `role`, `no_hp`, `nim`) VALUES
('Super Admin', 'admin@ngolab.com', SHA2('admin123', 256), 'superadmin', '081111111111', '1234567890')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`), `no_hp` = VALUES(`no_hp`), `nim` = VALUES(`nim`);
