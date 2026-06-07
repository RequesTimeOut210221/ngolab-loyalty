CREATE DATABASE IF NOT EXISTS `ngolab_loyalty`;
USE `ngolab_loyalty`;

CREATE TABLE IF NOT EXISTS `TABEL_USER` (
  `id_user` INT AUTO_INCREMENT PRIMARY KEY,
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
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) DEFAULT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `nim` VARCHAR(20) DEFAULT NULL,
  `nim_member` VARCHAR(20) DEFAULT NULL,
  `poin` INT NOT NULL DEFAULT 10,
  `saldo_poin` INT NOT NULL DEFAULT 10,
  `share_bonus` BOOLEAN NOT NULL DEFAULT FALSE,
  `api_key` VARCHAR(100) DEFAULT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `kategori_menu` (
  `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `menus` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_menu` VARCHAR(150) NOT NULL,
  `harga` INT NOT NULL,
  `kategori` VARCHAR(50) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `poin_didapat` INT NOT NULL DEFAULT 0,
  `is_promo` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `feedback` (
  `id_feedback` INT AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT DEFAULT NULL,
  `nama_user` VARCHAR(100) DEFAULT 'Anonim',
  `rating` TINYINT NOT NULL DEFAULT 5,
  `ulasan` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `TABEL_REWARD` (
  `id_reward` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_reward` VARCHAR(150) NOT NULL,
  `poin_dibutuhkan` INT NOT NULL,
  `stok` INT NOT NULL DEFAULT 0,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `TABEL_PESANAN` (
  `id_pesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `id_menu` INT NOT NULL,
  `jumlah` INT NOT NULL,
  `total_harga` INT NOT NULL,
  `poin_didapat` INT NOT NULL,
  `catatan_pesanan` TEXT,
  `status` ENUM('pending', 'diproses', 'selesai') NOT NULL DEFAULT 'pending',
  `tanggal_pesan` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_user`) REFERENCES `TABEL_USER`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_menu`) REFERENCES `menus`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `TABEL_PENUKARAN_REWARD` (
  `id_penukaran` INT AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `id_reward` INT NOT NULL,
  `tanggal_tukar` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'berhasil', 'ditolak') NOT NULL DEFAULT 'pending',
  `token_wifi` VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (`id_user`) REFERENCES `TABEL_USER`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_reward`) REFERENCES `TABEL_REWARD`(`id_reward`) ON DELETE CASCADE
);

INSERT INTO `kategori_menu` (`nama_kategori`, `deskripsi`) VALUES
('cafe', 'Berbagai varian kopi, non-kopi, dan menu cafe'),
('bakso', 'Menu bakso dan makanan hangat')
ON DUPLICATE KEY UPDATE `deskripsi` = VALUES(`deskripsi`);

INSERT INTO `menus` (`nama_menu`, `harga`, `kategori`, `deskripsi`, `gambar`, `poin_didapat`) VALUES
('Kopi Susu Gula Aren', 15000, 'cafe', 'Espresso blend khas Ngolab dengan susu segar dan gula aren.', 'Test1.jpg', 1),
('Matcha Latte', 20000, 'cafe', 'Matcha latte creamy untuk teman nugas.', 'Test2.jpg', 2),
('Bakso Urat Spesial', 22000, 'bakso', 'Bakso urat dengan kuah kaldu gurih.', 'Test3.png', 2),
('Bakso Telur', 20000, 'bakso', 'Bakso isi telur dengan kuah hangat.', 'Test4.jpg', 2)
ON DUPLICATE KEY UPDATE `harga` = VALUES(`harga`);

INSERT INTO `TABEL_REWARD` (`nama_reward`, `poin_dibutuhkan`, `stok`, `gambar`) VALUES
('WiFi VIP Voucher 24 Jam', 5, 99, 'https://placehold.co/300x300?text=WiFi+VIP'),
('Kopi Susu Aren Gratis', 15, 20, 'uploads/menus/Test1.jpg'),
('Bakso Urat Gratis', 25, 12, 'uploads/menus/Test3.png')
ON DUPLICATE KEY UPDATE `stok` = VALUES(`stok`);

INSERT INTO `TABEL_STAFF` (`username`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin@ngolab.com', SHA2('admin123', 256), 'superadmin')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`);
