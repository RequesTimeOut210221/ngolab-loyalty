
USE ngolab_db;
CREATE TABLE IF NOT EXISTS kategori_menu (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS feedback (
    id_feedback INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT DEFAULT NULL,
    nama_user VARCHAR(100) DEFAULT 'Anonim',
    rating TINYINT NOT NULL DEFAULT 5,
    ulasan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS menus (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    nama_menu VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    gambar_menu VARCHAR(255) DEFAULT NULL
);

INSERT INTO kategori_menu (nama_kategori, deskripsi) VALUES
('Kopi', 'Berbagai varian kopi espresso dan manual brew'),
('Non-Kopi', 'Minuman non-kafein seperti matcha, cokelat, teh'),
('Bakso Kuah', 'Bakso dengan kuah kaldu sapi segar'),
('Bakso Goreng', 'Bakso goreng crispy dan snack');


INSERT INTO feedback (nama_user, rating, ulasan) VALUES
('Budi Raharjo', 5, 'Kopi susu arennya juara banget! Suasana cafe juga nyaman buat nugas.'),
('Ani Wijaya', 4, 'Bakso uratnya mantap, kuahnya gurih. Cuma antrinya agak lama.'),
('Reza Pratama', 5, 'WiFi kencang, kopi enak, poin loyalty bermanfaat. Recommended!');
