# PEMBAGIAN TUGAS TIM: NGOLAB LOYALTY & MANAGEMENT APP
**Jumlah Anggota:** 3 Orang

Sesuai ketentuan proyek, masing-masing anggota minimal wajib membuat:
- 2 CRUD (Create, Read, Update, Delete) berbasis PHP Native penuh (Monolitik di sisi Admin).
- 2 Endpoint API dengan minimal 2 Method HTTP per endpoint (untuk diakses oleh Frontend Konsumen).

---

## 👥 MEMBER 1: MANAJEMEN PENGGUNA & AUTHENTICATION - Mas'ud
Fokus pada alur pendaftaran, login gabungan (Unified Login), otentikasi API Key, serta pengelolaan profil & data pengguna.

### 🛠️ 1. CRUD (Full PHP - Admin Panel)
- **CRUD Kelola Member (`admin/kelola_member.php`)**:
  - **Read**: Menampilkan daftar member, level tier, total belanja, dan saldo poin.
  - **Update**: Edit data member manual (NIM, nomor HP, nama, saldo poin).
  - **Delete**: Hapus member.
  - **Promote/Demote**: Hak khusus Superadmin (SUDO) untuk mengubah role member menjadi admin atau sebaliknya.
- **CRUD Kelola Staff (`admin/kelola_staff.php`)**:
  - **Create**: Tambah akun staff admin baru (dengan enkripsi password).
  - **Read**: Tampil daftar staff admin.
  - **Update**: Edit username/password staff.
  - **Delete**: Hapus akun staff admin.

### 🔌 2. API Endpoints
- **API Auth (`api/auth.php`)**:
  - `POST /api/auth.php?action=login` - Login member menggunakan nomor HP, mengembalikan API Key.
  - `POST /api/auth.php?action=register` - Registrasi member baru. Secara otomatis mendapat Welcome Bonus **+10 Poin**.
- **API Profil (`api/profile.php`)**:
  - `GET /api/profile.php` - Mengambil detail profil member, tier, dan saldo poin saat ini (membawa `x-api-key`).
  - `POST /api/profile.php?action=update` - Update nama lengkap & upload avatar/foto profil ke folder `uploads/profiles/`.

### 📱 3. Frontend Pages & JS
- **Unified Login Page (`admin/login.php`)**: Logika form login terpadu.
- **Halaman Profil Konsumen (`profil.html` & `assets/js/profile.js`)**: Integrasi dengan API Profil untuk render data, upload foto, dan kelola UI (Catatan: profil.html merujuk/mengarahkan ke tab Profil di index.html).

---

## 👥 MEMBER 2: MANAJEMEN KATALOG & ULASAN (CONTENT & REVIEWS) - Ayesha
Fokus pada pengelolaan menu kafe/bakso (Hybrid Catalog), kategori menu, serta sistem ulasan pelanggan (Feedback).

### 🛠️ 1. CRUD (Full PHP - Admin Panel)
- **CRUD Kelola Menu (`admin/kelola_menu.php`)**:
  - **Create**: Menambahkan menu baru (Nama, Harga, Kategori Menu: cafe/bakso, upload gambar menu).
  - **Read**: List menu kafe dan bakso.
  - **Update**: Update detail menu dan gambar menu.
  - **Delete**: Hapus menu.
- **CRUD Kelola Kategori (`admin/kelola_kategori.php`)**:
  - **Create**: Tambah kategori hadiah/reward.
  - **Read**: Tampil daftar kategori.
  - **Update**: Edit kategori.
  - **Delete**: Hapus kategori.
- **Log Feedback/Ulasan (`admin/kelola_feedback.php`)**:
  - **Read**: Menampilkan semua feedback/rating dari konsumen.
  - **Delete**: Menghapus ulasan yang tidak pantas.

### 🔌 2. API Endpoints
- **API Menus (`api/menus.php`)**:
  - `GET /api/menus.php` - Mendapatkan daftar menu berdasarkan filter kategori (cafe / bakso).
  - `POST /api/menus.php` - Input menu baru secara terprogram (misal integrasi dengan partner).
- **API Feedback (`api/feedback.php`)**:
  - `POST /api/feedback.php` - Mengirim rating & ulasan dari member. Memberikan bonus **+5 Poin** otomatis (maksimal 1x per pesanan).
  - `GET /api/feedback.php` - Mengambil daftar ulasan publik untuk testimoni.

### 📱 3. Frontend Pages & JS
- **Halaman Utama Konsumen (`index.html` & `assets/js/app.js`)**: Render Katalog Menu Hybrid (Toggle Kopi Kafe vs Bakso Mas Yanto) menggunakan API Menus.

---

## 👥 MEMBER 3: TRANSAKSI & REDEMPTION (LOYALTY SYSTEM) - Shaena
Fokus pada alur belanja mandiri (Self-Order Checkout), penukaran poin (Redemption), dan penyesuaian saldo poin member berdasarkan status pesanan.

### 🛠️ 1. CRUD (Full PHP - Admin Panel)
- **CRUD Kelola Pesanan (`admin/kelola_pesanan.php` - Mock POS)**:
  - **Read**: Melihat daftar pesanan masuk dari konsumen.
  - **Update**: Mengubah status pesanan (`pending` -> `diproses` -> `selesai`). **Ketika status diset 'selesai' (Lunas), poin belanja secara otomatis ditambahkan ke saldo poin user** (Kelipatan Rp 10.000 = 1 Poin).
  - **Delete/Cancel**: Batalkan pesanan.
- **CRUD Kelola Reward (`admin/kelola_reward.php`)**:
  - **Create**: Menambahkan reward/hadiah baru (Nama, Harga Poin, Kategori, Stok, Batasan Tier Level).
  - **Read**: List katalog reward.
  - **Update**: Edit data reward.
  - **Delete**: Hapus reward.
- **CRUD Kelola Penukaran (`admin/kelola_penukaran.php`)**:
  - **Read**: Daftar klaim penukaran poin oleh member.
  - **Update**: Konfirmasi / Tolak penukaran voucher/hadiah.

### 🔌 2. API Endpoints
- **API Checkout (`api/checkout.php`)**:
  - `POST /api/checkout.php` - Membuat pesanan baru dari menu-menu di keranjang belanja. Menghitung poin potensial yang didapat.
  - `GET /api/checkout.php` - Mendapatkan riwayat belanja pesanan untuk member yang login.
- **API Redemptions (`api/redemptions.php`)**:
  - `POST /api/redemptions.php` - Request penukaran poin dengan item reward tertentu. Mengurangi poin member secara real-time jika disetujui.
  - `GET /api/redemptions.php` - Riwayat penukaran reward member (ditampilkan di tab Katalog Reward / Riwayat di `index.html`).
- **API Share Bonus (`api/share_bonus.php`)**:
  - `POST /api/share_bonus.php` - Memberikan bonus **+10 Poin** jika user melakukan share ke sosial media (Instagram/X) untuk pertama kalinya.

### 📱 3. Frontend Pages & JS
- **Keranjang & Checkout (`assets/js/app.js`)**: Logic DOM keranjang belanja, hitung poin otomatis saat nominal diketik/dihitung, dan POST Checkout.
- **Klaim Reward & WiFi (`assets/js/profile.js`)**: Logic penukaran reward di tab Katalog Reward, popover status redeem, dan claim token WiFi.
