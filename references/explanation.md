# Penjelasan Struktur Proyek Ngolab Loyalty

Dokumen ini memberikan penjelasan mengenai setiap direktori dan berkas yang terdapat di dalam repositori **Ngolab Loyalty**.

## 1. Direktori Utama

- `index.html`: Merupakan halaman utama (Single Page Application) yang diakses oleh konsumen/member. Semua tampilan antarmuka front-end seperti katalog menu, keranjang, profil, dan penukaran poin dijalankan di sini secara dinamis menggunakan JavaScript.
- `README.md`: Panduan utama bagi pengembang terkait cara menjalankan proyek, pembagian tugas (role), dan panduan pengujian menggunakan akun default.

## 2. Direktori `config/` (Konfigurasi Aplikasi)

- `koneksi.php`: Berkas penting untuk menghubungkan backend PHP dengan database MySQL. Memuat konfigurasi seperti _host_, _username_, _password_, dan _nama database_.

## 3. Direktori `references/` (Dokumentasi)

- `explanation.md`: Berkas ini. Menjelaskan rincian fungsi setiap berkas dan folder di dalam repositori.
- `Struktur Folder & File.md`: Rangkuman struktur direktori repositori.

## 4. Direktori `prototypes/` (Prototipe UI)

- `showcase.html`: Berkas ini digunakan sebagai galeri komponen UI dan prototipe. Disini Anda dapat melihat semua elemen desain seperti tombol, kartu, dan toast notification tanpa logika bisnis (hanya tampilan statis).

## 5. Direktori `tests/`

- `test_db.php`: Skrip pengujian sederhana untuk memverifikasi apakah koneksi ke database MySQL berhasil.

## 6. Direktori `admin/` (Panel Admin Backend)

Direktori ini berisi halaman-halaman antarmuka admin yang dibuat secara native dengan PHP dan HTML.

- `admin_dashboard.php`: Halaman utama dan "cangkang" (layout) panel admin. Menangani sesi login staff dan melakukan inklusi (include) halaman-halaman kelola lainnya menggunakan parameter URL `?page=`.
- `kelola_pesanan.php`:(Tugas Shaena) Halaman untuk melihat dan mengubah status pesanan pelanggan. Mengubah status pesanan menjadi "selesai" akan secara otomatis menambah poin ke member.
- `kelola_penukaran.php`:(Tugas Shaena) Halaman untuk menyetujui atau menolak pengajuan penukaran poin (reward) oleh member. Jika ditolak, poin member akan otomatis dikembalikan (di-refund).
- `kelola_menu.php`: (Tugas Ayesha) Halaman untuk CRUD (Create, Read, Update, Delete) menu-menu kafe atau restoran.
- `kelola_kategori.php`: (Tugas Ayesha) Halaman CRUD kategori menu dan reward.
- `kelola_feedback.php`: (Tugas Ayesha) Halaman untuk memantau ulasan/rating pelanggan.
- `kelola_member.php`: (Tugas Mas'ud) Halaman CRUD data pengguna/member yang terdaftar.
- `kelola_staff.php`: (Tugas Mas'ud) Halaman CRUD data pegawai/admin pengelola situs.
- `kelola_reward.php`: (Tugas Shaena) Halaman CRUD daftar item hadiah yang bisa ditukar dengan poin.

## 3. Direktori `api/` (Endpoint Backend untuk Frontend SPA)

Direktori ini berisi skrip PHP yang merespons permintaan AJAX/Fetch dari `app.js` menggunakan format JSON.

- `auth.php`: (Tugas Mas'ud) Menangani proses Registrasi akun baru (mencatat ke tabel `TABEL_MEMBER`) dan Login. Otentikasi menggunakan _API Key_ berbasis Header (`x-api-key`).
- `categories.php`: (Tugas Ayesha) Mengembalikan daftar `TABEL_KATEGORI_MENU` untuk pengelompokan menu kafe.
- `checkout.php`: (Tugas Shaena) Menerima data keranjang belanja dan memproses pesanan baru ke `TABEL_PESANAN`.
- `feedback.php`: (Tugas Ayesha) Menerima input ulasan dari member serta memberikan bonus +5 Poin secara otomatis.
- `menus.php`: (Tugas Ayesha) Mengembalikan daftar menu dari `TABEL_MENU` beserta relasi `TABEL_KATEGORI_MENU`.
- `profile.php`: (Tugas Mas'ud) Digunakan untuk mendapatkan informasi detil member yang sedang login serta untuk memperbarui profil (seperti unggah foto avatar).
- `redemptions.php`: (Tugas Shaena) Memproses penukaran poin dari member. Jika saldo cukup, poin akan dikurangi dari `TABEL_MEMBER` dan status dimasukkan ke `TABEL_PENUKARAN_REWARD`.
- `rewards.php`: Mengembalikan daftar `TABEL_REWARD` beserta nama kategori dari `TABEL_KATEGORI_REWARD`.
- `share_bonus.php`: (Tugas Shaena) Memproses klaim bonus +10 poin untuk member yang melakukan klik _Share Social Media_ (Hanya bisa dilakukan 1 kali per akun).

## 4. Direktori `assets/` (Frontend Resources)

Berisi semua aset yang berjalan di sisi _client_ (browser).

- **`assets/js/app.js`**: Skrip pusat (Controller) untuk SPA. Menangani routing antar-tab (home, menu, history), keranjang belanja (cart state), filter & pencarian katalog, serta _render_ DOM dinamis (misal: merender kartu menu ke `catalog-grid`).
- **`assets/js/api.js`**: Kumpulan fungsi `fetch()` pembungkus. Digunakan oleh `app.js` untuk meminta data dari direktori `/api/` (misal: `ApiService.login()`, `ApiService.getMenus()`). Termasuk fungsi _Local Storage / Cache_.
- **`assets/js/profile.js`**: Skrip khusus untuk menangani fungsionalitas di halaman/tab Profil, seperti upload avatar secara real-time dan merender _Katalog Reward_ (dengan fungsi pencarian dan filter kategori).
- **`assets/css/`**: Berkas _styling_ lokal untuk kostumisasi yang tidak tersedia di kelas utilitas bawaan Tailwind CDN.
- **`assets/uploads/`**: Direktori tempat menyimpan file hasil unggahan. Biasanya berisi subdirektori seperti `menus/`, `rewards/`, dan `profiles/`.

## 5. Direktori `database/`

- `schema.sql`: Berkas skrip SQL (_Data Definition Language_ dan _Data Manipulation Language_) yang berisi instruksi untuk:
  - Membuat basis data `ngolab_loyalty`.
  - Menginisialisasi seluruh skema tabel, mulai dari tabel member, produk, hingga log transaksi (`TABEL_MEMBER`, `TABEL_MENU`, `TABEL_PESANAN`, dll).
  - Melakukan insersi (_seeding_) data kategori default, menu default, hadiah, dan beberapa akun default untuk kebutuhan _testing_.
