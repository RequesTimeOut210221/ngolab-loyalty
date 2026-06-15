# Struktur Folder & File: Ngolab Loyalty & Self-Order System

**Jumlah Anggota:** 3 Orang (Mas'ud, Ayesha, Shaena)

Berikut adalah struktur folder final yang sudah diselaraskan dengan pembagian tugas di `Task.md`.

```
ngolab-loyalty/
│
├── index.html                 # [FRONTEND] Halaman Konsumen & Login 1-Pintu (SPA: Beranda, Menu, Profil)
├── README.md                  # Panduan pengembang, pembagian tugas, & akun default
│
├── config/                    # [BACKEND] Konfigurasi Aplikasi
│   └── koneksi.php            # [CRITICAL] File koneksi database global (Mas'ud)
│
├── database/                  # [BACKEND] Skema & Seed Database
│   └── schema.sql             # DDL + DML: Buat tabel, seed data default (Mas'ud)
│
├── tests/                     # [BACKEND] Skrip Pengujian
│   └── test_db.php            # Skrip tes koneksi database
│
├── prototypes/                # [FRONTEND] Prototipe & Komponen UI
│   └── showcase.html          # Galeri komponen UI (Mas'ud)
│
├── references/                # [DOCS] Referensi & Dokumentasi Tim
│   ├── Task.md                # Pembagian tugas detail (Source of Truth)
│   ├── explanation.md         # Penjelasan detil semua file
│   ├── Struktur Folder & File.md  # Berkas ini
│   ├── DESIGN.md              # Panduan desain UI
│   ├── FINAL Blueprint Mekanisme Perolehan Poin.md
│   ├── FINAL Entity Relationship Diagram.md
│   ├── INFO PROJECT WEB.md
│   ├── improvement.md
│   ├── layout_main.md
│   └── layout_mobile.md
│
├── assets/                    # [FRONTEND] Aset Statis
│   ├── css/
│   │   └── style.css          # Styling utama (Mas'ud)
│   ├── js/
│   │   ├── app.js             # Logika DOM & State Keranjang Belanja (Self-Order)
│   │   ├── profile.js         # Logika DOM khusus profil (Render poin, upload avatar)
│   │   └── api.js             # Kumpulan fungsi fetch() utama + Mock Fallback
│   └── uploads/               # Media / Penyimpanan File Upload
│       ├── profiles/          # Gambar Avatar/Foto Profil Member
│       ├── rewards/           # Gambar item hadiah
│       └── menus/             # Gambar menu kafe/bakso
│
├── api/                       # [BACKEND] Endpoint JSON (Wajib menggunakan x-api-key)
│   ├── middleware.php         # (Mas'ud) Validasi header `x-api-key`
│   │
│   ├── auth.php               # (Mas'ud) POST Login & Register
│   ├── profile.php            # (Mas'ud) GET Profil & Poin Member, POST Update Profil/Foto
│   │
│   ├── menus.php              # (Ayesha) GET Katalog Menu (filter: cafe/bakso)
│   ├── categories.php         # (Ayesha) GET Daftar Kategori Menu
│   ├── feedback.php           # (Ayesha) POST Kirim Ulasan (+5 Poin), GET Ulasan Publik
│   │
│   ├── checkout.php           # (Shaena) POST Pesanan Baru, GET Riwayat Belanja
│   ├── redemptions.php        # (Shaena) POST Tukar Poin, GET Riwayat Penukaran
│   ├── rewards.php            # (Shaena) GET Daftar Reward untuk Konsumen
│   └── share_bonus.php        # (Shaena) POST Klaim Bonus +10 Poin Share Medsos
│
└── admin/                     # [BACKEND] Dashboard PHP Monolithic
    ├── admin_dashboard.php    # (Mas'ud) Layout Shell, Sidebar, Sesi, & Routing ?page=
    ├── layout_sidebar.php     # (Mas'ud) Komponen sidebar navigasi admin
    ├── logout.php             # (Mas'ud) Logout & destroy session
    │
    ├── kelola_member.php      # (Mas'ud) CRUD Data Member
    ├── kelola_staff.php       # (Mas'ud) CRUD Data Staff Admin
    │
    ├── kelola_menu.php        # (Ayesha) CRUD Master Menu Kafe & Bakso
    ├── kelola_kategori.php    # (Ayesha) CRUD Master Kategori Menu/Reward
    ├── kelola_feedback.php    # (Ayesha) Read & Delete Ulasan Pelanggan
    │
    ├── kelola_pesanan.php     # (Shaena) Baca pesanan masuk, Update status (Trigger +Poin)
    ├── kelola_reward.php      # (Shaena) CRUD Master Reward/Hadiah
    └── kelola_penukaran.php   # (Shaena) Konfirmasi/Tolak penukaran reward
```

---

## Ringkasan Pembagian Per Anggota

### 👤 Mas'ud — Manajemen Pengguna & Authentication + Infrastruktur
| Tipe | File | Fungsi |
|------|------|--------|
| Infrastruktur | `config/koneksi.php` | Koneksi database global |
| Infrastruktur | `database/schema.sql` | Skema & seed database |
| Infrastruktur | `api/middleware.php` | Validasi API Key |
| Infrastruktur | `admin/admin_dashboard.php` | Shell admin, routing, sesi |
| Infrastruktur | `admin/layout_sidebar.php` | Komponen sidebar navigasi |
| Infrastruktur | `admin/logout.php` | Logout session |
| Infrastruktur | `prototypes/showcase.html` | Galeri komponen UI |
| Infrastruktur | `assets/css/style.css` | Styling utama |
| CRUD Admin | `admin/kelola_member.php` | CRUD data member |
| CRUD Admin | `admin/kelola_staff.php` | CRUD data staff admin |
| API Endpoint | `api/auth.php` | Login & Register member |
| API Endpoint | `api/profile.php` | Profil & upload avatar |
| Frontend | `index.html` (Login & Profil) | Unified Login + tab Profil |
| Frontend | `assets/js/profile.js` | Logika profil konsumen |

### 👤 Ayesha — Manajemen Katalog & Ulasan
| Tipe | File | Fungsi |
|------|------|--------|
| CRUD Admin | `admin/kelola_menu.php` | CRUD menu kafe & bakso |
| CRUD Admin | `admin/kelola_kategori.php` | CRUD kategori menu/reward |
| CRUD Admin | `admin/kelola_feedback.php` | Pantau & hapus ulasan |
| API Endpoint | `api/menus.php` | Daftar menu (filter kategori) |
| API Endpoint | `api/categories.php` | Daftar kategori menu |
| API Endpoint | `api/feedback.php` | Kirim & baca ulasan (+5 Poin) |
| Frontend | `index.html` (tab Menu) | Render katalog menu hybrid |

### 👤 Shaena — Transaksi & Redemption (Loyalty System)
| Tipe | File | Fungsi |
|------|------|--------|
| CRUD Admin | `admin/kelola_pesanan.php` | Kelola pesanan (trigger +Poin) |
| CRUD Admin | `admin/kelola_reward.php` | CRUD reward/hadiah |
| CRUD Admin | `admin/kelola_penukaran.php` | Konfirmasi/tolak penukaran |
| API Endpoint | `api/checkout.php` | Buat pesanan & riwayat belanja |
| API Endpoint | `api/redemptions.php` | Tukar poin & riwayat klaim |
| API Endpoint | `api/rewards.php` | Daftar reward untuk konsumen |
| API Endpoint | `api/share_bonus.php` | Klaim bonus share medsos (+10 Poin) |
| Frontend | `assets/js/app.js` (Keranjang) | Logic keranjang & checkout |
| Frontend | `assets/js/profile.js` (Reward) | Logic klaim reward & WiFi |

---

## Penjelasan Flow Profil

1. **Baca Data:** Ketika user membuka tab Profil di `index.html`, file `profile.js` menjalankan `fetch('api/profile.php', { method: 'GET' })` dengan header API Key. Backend mengembalikan data JSON (Nama, Saldo Poin, URL Foto Profil) untuk dirender di layar.

2. **Histori:** Di halaman profil juga memanggil `fetch('api/redemptions.php', { method: 'GET' })` milik Shaena untuk menampilkan riwayat "Kapan saja user ini menukar poin".

3. **Edit Profil:** Jika user mengganti foto/nama, fetch akan mengirim `POST` (dengan `multipart/form-data` untuk upload foto) ke `api/profile.php` untuk mengupdate `TABEL_MEMBER`.

---

## Penjelasan Sinkronisasi Alur

1. **Frontend (`index.html`):** User memilih menu dari `/api/menus.php` (Ayesha) lalu memasukkannya ke keranjang belanja JavaScript (`app.js`).

2. **Kirim Pesanan:** Saat user menekan "Bayar/Pesan", sistem menembak `/api/checkout.php` milik **Shaena** untuk menyimpan data ke database.

3. **Pencairan Poin:** Admin buka `/admin/kelola_pesanan.php` (tugas **Shaena**). Admin akan melihat pesanan berstatus "Pending". Begitu Admin klik "Selesai/Lunas", PHP mengeksekusi _update query_ yang menyuntikkan poin ke akun konsumen.

---

## Aturan Kolaborasi GitHub (Mencegah Conflict)

1. **Dilarang keras** mengubah isi file `config/koneksi.php` di GitHub. Jika kredensial database lokal berbeda (misal password `root` kosong), cukup ubah di laptop masing-masing tapi **jangan di-push/commit** perubahan tersebut.

2. Setiap anggota **hanya bekerja** di file `kelola_*.php` dan `*.php` di dalam folder `/api/` yang menjadi tanggung jawabnya.

3. Untuk pekerjaan di `/assets/js/app.js`, `profile.js`, dan `index.html`, lakukan **komunikasi di grup** sebelum melakukan `git push` agar tidak terjadi tumpang tindih.
