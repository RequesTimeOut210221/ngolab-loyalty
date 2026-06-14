# Penjelasan Struktur Proyek Ngolab Loyalty

Dokumen ini memberikan penjelasan mengenai setiap direktori dan berkas yang terdapat di dalam repositori **Ngolab Loyalty**.

## 1. Direktori Utama
- `index.html`: Merupakan halaman utama (Single Page Application) yang diakses oleh konsumen/member. Semua tampilan antarmuka front-end seperti katalog menu, keranjang, profil, dan penukaran poin dijalankan di sini secara dinamis menggunakan JavaScript.
- `showcase.html`: Berkas ini digunakan sebagai galeri komponen UI dan prototipe. Disini Anda dapat melihat semua elemen desain seperti tombol, kartu, dan toast notification tanpa logika bisnis (hanya tampilan statis).
- `koneksi.php`: Berkas penting untuk menghubungkan backend PHP dengan database MySQL. Memuat konfigurasi seperti *host*, *username*, *password*, dan *nama database*.
- `README.md`: Panduan utama bagi pengembang terkait cara menjalankan proyek, pembagian tugas (role), dan panduan pengujian menggunakan akun default.
- `explanation.md`: Berkas ini. Menjelaskan rincian fungsi setiap berkas dan folder di dalam repositori.

## 2. Direktori `admin/` (Panel Admin Backend)
Direktori ini berisi halaman-halaman antarmuka admin yang dibuat secara native dengan PHP dan HTML.
- `admin_dashboard.php`: Halaman utama dan "cangkang" (layout) panel admin. Menangani sesi login staff dan melakukan inklusi (include) halaman-halaman kelola lainnya menggunakan parameter URL `?page=`.
- `kelola_pesanan.php`: Halaman untuk melihat dan mengubah status pesanan pelanggan. Mengubah status pesanan menjadi "selesai" akan secara otomatis menambah poin ke member.
- `kelola_penukaran.php`: Halaman untuk menyetujui atau menolak pengajuan penukaran poin (reward) oleh member. Jika ditolak, poin member akan otomatis dikembalikan (di-refund).
- `kelola_menu.php`: (Tugas Ayesha) Halaman untuk CRUD (Create, Read, Update, Delete) menu-menu kafe atau restoran.
- `kelola_kategori.php`: (Tugas Ayesha) Halaman CRUD kategori menu dan reward.
- `kelola_feedback.php`: (Tugas Ayesha) Halaman untuk memantau ulasan/rating pelanggan.
- `kelola_member.php`: (Tugas Mas'ud) Halaman CRUD data pengguna/member yang terdaftar.
- `kelola_staff.php`: (Tugas Mas'ud) Halaman CRUD data pegawai/admin pengelola situs.
- `kelola_reward.php`: (Tugas Shaena) Halaman CRUD daftar item hadiah yang bisa ditukar dengan poin.

## 3. Direktori `api/` (Endpoint Backend untuk Frontend SPA)
Direktori ini berisi skrip PHP yang merespons permintaan AJAX/Fetch dari `app.js` menggunakan format JSON.
- `auth.php`: Menangani proses Registrasi akun baru (mencatat ke tabel `TABEL_MEMBER`) dan Login. Otentikasi menggunakan *API Key* berbasis Header (`x-api-key`).
- `categories.php`: Mengembalikan daftar `TABEL_KATEGORI_MENU` untuk pengelompokan menu kafe.
- `checkout.php`: Menerima data keranjang belanja dan memproses pesanan baru ke `TABEL_PESANAN`.
- `feedback.php`: Menerima input ulasan dari member serta memberikan bonus +5 Poin secara otomatis.
- `menus.php`: Mengembalikan daftar menu dari `TABEL_MENU` beserta relasi `TABEL_KATEGORI_MENU`.
- `profile.php`: Digunakan untuk mendapatkan informasi detil member yang sedang login serta untuk memperbarui profil (seperti unggah foto avatar).
- `redemptions.php`: Memproses penukaran poin dari member. Jika saldo cukup, poin akan dikurangi dari `TABEL_MEMBER` dan status dimasukkan ke `TABEL_PENUKARAN_REWARD`.
- `rewards.php`: Mengembalikan daftar `TABEL_REWARD` beserta nama kategori dari `TABEL_KATEGORI_REWARD`.
- `share_bonus.php`: Memproses klaim bonus +10 poin untuk member yang melakukan klik *Share Social Media* (Hanya bisa dilakukan 1 kali per akun).

## 4. Direktori `assets/` (Frontend Resources)
Berisi semua aset yang berjalan di sisi *client* (browser).
- **`assets/js/app.js`**: Skrip pusat (Controller) untuk SPA. Menangani routing antar-tab (home, menu, history), keranjang belanja (cart state), filter & pencarian katalog, serta *render* DOM dinamis (misal: merender kartu menu ke `catalog-grid`).
- **`assets/js/api.js`**: Kumpulan fungsi `fetch()` pembungkus. Digunakan oleh `app.js` untuk meminta data dari direktori `/api/` (misal: `ApiService.login()`, `ApiService.getMenus()`). Termasuk fungsi *Local Storage / Cache*.
- **`assets/js/profile.js`**: Skrip khusus untuk menangani fungsionalitas di halaman/tab Profil, seperti upload avatar secara real-time dan merender *Katalog Reward* (dengan fungsi pencarian dan filter kategori).
- **`assets/css/`**: Berkas *styling* lokal untuk kostumisasi yang tidak tersedia di kelas utilitas bawaan Tailwind CDN.
- **`assets/uploads/`**: Direktori tempat menyimpan file hasil unggahan. Biasanya berisi subdirektori seperti `menus/`, `rewards/`, dan `profiles/`.

## 5. Direktori `database/`
- `schema.sql`: Berkas skrip SQL (*Data Definition Language* dan *Data Manipulation Language*) yang berisi instruksi untuk:
  - Membuat basis data `ngolab_loyalty`.
  - Menginisialisasi seluruh skema tabel, mulai dari tabel member, produk, hingga log transaksi (`TABEL_MEMBER`, `TABEL_MENU`, `TABEL_PESANAN`, dll).
  - Melakukan insersi (*seeding*) data kategori default, menu default, hadiah, dan beberapa akun default untuk kebutuhan *testing*.
