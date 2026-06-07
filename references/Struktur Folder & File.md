Struktur Folder & File: Ngolab Express Cafe (Self-Order & User Profile)

Berikut adalah struktur folder final yang sudah mencakup sistem manajemen profil pengguna (profil.html dan /api/profile.php) yang menjadi tugas dari Anggota 1.

ngolab-express-cafe/
│
├── index.html                 # [FRONTEND] Halaman Konsumen & Login 1-Pintu (Beranda, Menu, Profil)
├── koneksi.php                # [CRITICAL] File koneksi database global
├── README.md                  # Dokumentasi API Key untuk Assessment 3
│
├── assets/                    # [FRONTEND] Aset Statis
│   ├── css/style.css
│   └── js/
│       ├── app.js             # Logika DOM & State Keranjang Belanja (Self-Order)
│       ├── profile.js         # Logika DOM khusus profil (Render poin, upload avatar)
│       └── api.js             # Kumpulan fungsi fetch() utama
│
├── uploads/                   # [BACKEND] Media / Penyimpanan File
│   ├── profiles/              # [BARU] Gambar-gambar Avatar/Foto Profil Member
│   ├── rewards/               # Gambar item hadiah
│   └── menus/                 # Gambar menu kopi
│
├── api/                       # [BACKEND] Endpoint JSON (Wajib menggunakan x-api-key)
│   ├── middleware.php         # Validasi `x-api-key`
│   │
│   ├── auth.php               # (Anggota 1) POST Login & Register
│   ├── profile.php            # (Anggota 1) [DIUBAH] GET Profil & Poin Member, PUT Update Profil/Foto
│   │
│   ├── rewards.php            # (Anggota 2) GET List Reward
│   ├── categories.php         # (Anggota 2) GET Kategori
│   │
│   ├── checkout.php           # (Anggota 3) POST Pesanan Masuk (Mock POS)
│   ├── redemptions.php        # (Anggota 3) POST Tukar Poin & GET Histori (Ditampilkan di profil.html)
│   │
│   ├── menus.php              # (Anggota 4) GET Katalog Kopi/Menu
│   └── feedback.php           # (Anggota 4) POST Kirim Ulasan
│
└── admin/                     # [BACKEND] Dashboard PHP Monolithic
    ├── admin_dashboard.php    # (Layout Sidebar & Shell Dashboard)
    ├── logout.php
    │
    ├── kelola_member.php      # (Anggota 1) CRUD Data Member (Admin bisa hapus member nakal)
    ├── kelola_staff.php       # (Anggota 1) CRUD Data Staff
    │
    ├── kelola_reward.php      # (Anggota 2) CRUD Master Reward
    ├── kelola_kategori.php    # (Anggota 2) CRUD Master Kategori
    │
    ├── kelola_pesanan.php     # (Anggota 3) Baca pesanan masuk, Update status (Trigger +Poin)
    ├── kelola_penukaran.php   # (Anggota 3) Konfirmasi/Tolak penukaran reward
    │
    ├── kelola_menu.php        # (Anggota 4) CRUD Master Menu Kafe
    └── kelola_feedback.php    # (Anggota 4) Read & Delete Ulasan Pelanggan


Penjelasan Flow Profil:

Baca Data: Ketika user membuka profil.html, file profile.js akan menjalankan fetch('api/profile.php', { method: 'GET' }) sambil membawa API Key. Backend mengembalikan data JSON (Nama, Saldo Poin, URL Foto Profil) untuk dirender di layar.

Histori: Di halaman profil juga memanggil fetch('api/redemptions.php', { method: 'GET' }) milik Anggota 3 untuk menampilkan riwayat "Kapan saja user ini menukar poin".

Edit Profil: Jika user mengganti foto/nama di profil, fetch akan mengirim method PUT (atau POST dengan multipart/form-data untuk upload foto) ke api/profile.php untuk mengupdate tabel_user.

### Penjelasan Sinkronisasi Alur:

1. **Frontend (index.html):** User memilih menu dari `/api/menus` lalu memasukkannya ke keranjang belanja JavaScript (`app.js`).

2. **Kirim Pesanan:** Saat user menekan "Bayar/Pesan", sistem menembak `/api/checkout.php` milik **Anggota 3** untuk menyimpan data ke database.

3. **Pencairan Poin:** Admin buka `/admin/kelola_pesanan.php` (tugas **Anggota 3**). Admin akan melihat pesanan tersebut berstatus "Pending". Begitu Admin klik "Selesai/Lunas", PHP akan mengeksekusi _update query_ yang menyuntikkan poin hasil kelipatan belanja ke akun konsumen.


## Aturan Kolaborasi GitHub (Mencegah Conflict)

1. **Dilarang keras** mengubah isi file `koneksi.php` jika formatnya sudah disepakati di Sprint 0. Jika kredensial database lokal berbeda (misal password `root` kosong atau tidak), cukup ubah di laptop masing-masing tapi **jangan di-push/commit** perubahan `koneksi.php` tersebut.

2. Setiap anggota **hanya bekerja** di file `kelola_*.php` dan `*.php` di dalam folder `/api/` yang menjadi tanggung jawabnya (Sesuai pembagian 2 CRUD & 2 API per anggota).

3. Untuk pekerjaan di `/assets/js/app.js` dan `index.html`, lakukan komunikasi di grup sebelum melakukan `git push` agar tidak terjadi tumpang tindih manipulasi DOM.
