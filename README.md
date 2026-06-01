# Ngolab Loyalty & Self-Order System - Panduan Pengembang

Selamat datang di repositori proyek **Unified Loyalty Ecosystem (Ngo+Lab Cafe & Bakso Mas Yanto)**. Berkas ini berfungsi sebagai panduan kolaborasi tim, tata cara pengerjaan backend, serta petunjuk penggunaan purwarupa (prototype) front-end yang sudah siap.

---

## 🎨 1. Panduan Purwarupa Front-End (Showcase & Client-Side SPA)

Sisi konsumen dari web ini dirancang sebagai **Single Page Application (SPA)** yang interaktif dan responsif (nyaman di Desktop maupun Mobile). 

### Cara Menjalankan & Menguji Front-End:
1. **Menggunakan Live Server / PHP Server:**
   Buka direktori proyek ini menggunakan text editor (seperti VS Code) lalu jalankan ekstensi **Live Server**, ATAU jalankan built-in PHP server di terminal Anda:
   ```bash
   php -S localhost:8000
   ```
2. **Halaman Utama (SPA):**
   Akses `index.html` (atau `localhost:8000/index.html`). Halaman ini memuat seluruh tab (Beranda, Menu/Katalog, Katalog Reward, Riwayat, dan Profil) secara dinamis menggunakan modul JS.
3. **Galeri Komponen Tim (`showcase.html`):**
   Akses `showcase.html` (atau `localhost:8000/showcase.html`). Ini adalah pustaka visual komponen proyek. Anda dapat melihat palet warna, tipe kartu member (Bronze, Silver, Gold), badge status pesanan, loader, serta mencoba pemicu **Toast Notification** secara interaktif.

### 📡 Mekanisme Mock Fallback (Uji Coba Tanpa Database)
File `assets/js/api.js` dilengkapi dengan sistem **Automatic Mock Fallback**. 
* Jika backend PHP belum dibuat (endpoint mengembalikan error `404 Not Found` atau server database belum menyala), JavaScript akan otomatis mengalihkan pengambilan data ke **Mock DB** lokal di memori.
* Hal ini membuat fitur-fitur seperti *Login, Register, Tambah Keranjang, Checkout Pesanan, Misi Share Medsos, Kirim Feedback, hingga Klaim Voucher WiFi* **dapat dicoba secara interaktif langsung di browser saat ini juga**.
* **Integrasi Otomatis:** Begitu Anda membuat file PHP di folder `api/` (misal `api/menus.php`), JavaScript akan mendeteksi dan menggunakan respons asli dari database Anda secara otomatis tanpa perlu mengubah kode frontend.

---

## 👥 2. Pembagian Tugas & Tanggung Jawab Tim (Referensi: `Task.md`)

Masing-masing anggota bertanggung jawab penuh atas pembuatan **2 CRUD PHP Monolitik (Panel Admin)** dan **2 Endpoint API PHP (Sisi Konsumen)**.

### 👤 ANGGOTA 1: MANAJEMEN PENGGUNA & AUTHENTICATION
Fokus pada alur pendaftaran, login gabungan (Unified Login), otentikasi API Key, serta pengelolaan profil & data pengguna.

* **Tugas CRUD (Full PHP - Folder `admin/`):**
  * `admin/kelola_member.php`: Menampilkan daftar member, level tier, total belanja, saldo poin, edit member manual, hapus member, serta mengubah role (Superadmin privilege).
  * `admin/kelola_staff.php`: Mengelola akun staff admin baru (Tambah, Baca, Update, Hapus dengan enkripsi sandi).
* **Tugas API Endpoint (Folder `api/`):**
  * `api/auth.php`: Menangani login member via nomor HP (kembalikan API Key) dan registrasi member baru (+10 Poin Welcome Bonus).
  * `api/profile.php`: Mengambil detail profil member (`GET` dengan header `x-api-key`) dan mengupdate profil/unggah avatar (`POST/PUT` simpan di `uploads/profiles/`).
* **Halaman Terkait:** `admin/login.php` (Unified Login) dan integrasi frontend tab Profil.

---

### 👤 ANGGOTA 2: MANAJEMEN KATALOG & ULASAN (CONTENT & REVIEWS)
Fokus pada pengelolaan menu kafe/bakso (Hybrid Catalog), kategori menu, serta sistem ulasan pelanggan (Feedback).

* **Tugas CRUD (Full PHP - Folder `admin/`):**
  * `admin/kelola_menu.php`: Mengelola menu (Tambah, Baca, Edit, Hapus menu kopi & bakso beserta unggah foto menu ke `uploads/menus/`).
  * `admin/kelola_kategori.php`: Mengelola kategori menu/hadiah (Tambah, Baca, Edit, Hapus).
  * `admin/kelola_feedback.php`: Memantau ulasan/rating masuk dari member dan menghapus ulasan yang tidak pantas.
* **Tugas API Endpoint (Folder `api/`):**
  * `api/menus.php`: Menyediakan daftar menu (`GET`) berdasarkan filter kategori (`cafe`/`bakso`).
  * `api/feedback.php`: Menerima rating & ulasan dari member (`POST`) dan memberikan reward otomatis **+5 Poin** (dibatasi 1x per pesanan). `GET` untuk mengambil ulasan publik.
* **Halaman Terkait:** Integrasi katalog produk pada tab Menu konsumen.

---

### 👤 ANGGOTA 3: TRANSAKSI & REDEMPTION (LOYALTY SYSTEM)
Fokus pada alur belanja mandiri (Self-Order Checkout), penukaran poin (Redemption), dan penyesuaian saldo poin member berdasarkan status pesanan.

* **Tugas CRUD (Full PHP - Folder `admin/`):**
  * `admin/kelola_pesanan.php` (Mock POS): Melihat daftar pesanan masuk dari konsumen. Ketika Admin mengubah status pesanan dari `pending` menjadi `selesai` (Lunas), backend **wajib memicu penambahan poin** ke akun user (`TABEL_USER.saldo_poin += poin_didapat`) dengan kelipatan Rp 10.000 = 1 Poin.
  * `admin/kelola_reward.php`: Mengelola item hadiah yang bisa ditukar (Tambah, Baca, Edit, Hapus reward, stok, dan batasan tier di `uploads/rewards/`).
  * `admin/kelola_penukaran.php`: Konfirmasi / Tolak penukaran voucher/hadiah fisik oleh member.
* **Tugas API Endpoint (Folder `api/`):**
  * `api/checkout.php`: Menerima pembuatan pesanan baru (`POST`) dari keranjang belanja konsumen dan menghitung potensi poin yang didapat. `GET` untuk riwayat belanja member yang login.
  * `api/redemptions.php`: Memproses penukaran poin member (`POST`) dengan item reward pilihan (mengurangi poin secara real-time). `GET` untuk riwayat klaim voucher/WiFi (ditampilkan di Katalog Reward atau Riwayat).
  * `api/share_bonus.php`: Menangani klaim bonus medsos (`POST`) dengan memberikan **+10 Poin** jika member pertama kali melakukan klik share (ubah flag `is_shared_sosmed` di database jadi `true`).

---

## ⚠️ 3. Aturan Kolaborasi GitHub (Mencegah Conflict)

Untuk memastikan penggabungan kode (assessment 3) berjalan lancar tanpa konflik Git:

1. **Database & `koneksi.php`:**
   * **Dilarang keras mengubah atau menimpa berkas `koneksi.php` di GitHub.** 
   * Jika kredensial database lokal laptop Anda berbeda (misal password MySQL kosong atau nama database berbeda), edit berkas secara lokal namun **jangan commit/push** perubahan berkas `koneksi.php` tersebut.
2. **Batasan Berkas Tugas:**
   * Bekerjalah **hanya** pada file-file admin (`admin/kelola_*.php`) dan API (`api/*.php`) yang menjadi bagian tugas Anda masing-masing.
3. **Perubahan Berkas Front-End:**
   * Jika Anda perlu memodifikasi DOM atau fungsi di `index.html`, `assets/js/app.js`, atau `assets/css/style.css`, **komunikasikan terlebih dahulu di grup** sebelum melakukan `git push` untuk menghindari bentrokan perubahan (conflict).
4. **Penyimpanan File Upload:**
   * Pastikan gambar menu disimpan di `/uploads/menus/`, gambar reward di `/uploads/rewards/`, dan avatar profil di `/uploads/profiles/`.
