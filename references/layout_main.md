# Cetak Biru Layout Utama (Desktop & Responsive) - Ngolab Express Cafe

Dokumen ini berisi representasi tata letak (layout) utama berbasis **Desktop (PC/Laptop) dengan adaptabilitas Responsif** menggunakan ASCII Art untuk aplikasi loyalitas dan pemesanan mandiri **Ngolab Express Cafe**. 

---

## 💻 Sisi Konsumen: Desktop Layout (`index.html`)

Halaman konsumen dirancang sebagai **Single Page Application (SPA)**. Pada layar lebar (Desktop/PC/Laptop), navigasi berada di bagian atas (**Top Navbar**), dan ruang layar dimanfaatkan secara maksimal menggunakan sistem grid/kolom.

### A. Struktur Navigasi Atas (Top Navbar)
Navbar tetap (sticky) berada di bagian atas layar untuk akses instan ke semua tab, indikator status keranjang, dan profil ringkas.

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [☕] NGOLAB EXPRESS CAFE   [🏡 Beranda] [🍔 Katalog] [🎁 Reward] [🕒 Riwayat] [👤 Profil] [🛒 2] │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### B. Tampilan Tab Beranda (Home Dashboard View)
Pada Desktop, komponen beranda tersusun dalam **3 Kolom** sejajar untuk efisiensi ruang:
1. **Kolom 1:** Digital ID Card (berwarna sesuai Tier: Bronze/Silver/Gold).
2. **Kolom 2:** Gamifikasi/Progress target hadiah berikutnya.
3. **Kolom 3:** Fitur Akses Cepat (Redeem WiFi) & Misi sosial media.

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [☕] NGOLAB EXPRESS CAFE  [*Beranda*]  [Katalog]  [Reward]  [Riwayat]  [Profil]    [🛒 2] │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│  Selamat Datang Kembali, Budi Raharjo!                                                 │
│                                                                                        │
│  ┌──────────────────────────────┐ ┌──────────────────────────────┐ ┌────────────────┐  │
│  │ DIGITAL ID CARD (Silver Tier)│ │ 🎯 TARGET REWARD BERIKUTNYA  │ │ ⚡ AKSES CEPAT  │  │
│  │                              │ │                              │ │                │  │
│  │ Nama: Budi Raharjo           │ │ Kopi Susu Aren Gratis        │ │ [ Claim WiFi ] │  │
│  │ NIM : 1202210045             │ │ Progress: 55%                │ │ (Tukar 5 Poin) │  │
│  │                              │ │ ┌──────────────────────────┐ │ └────────────────┘  │
│  │ Saldo Poin:                  │ │ │████████████░░░░░░░░░░    │ │ 🎁 SHARE MISI    │  │
│  │ ┌──────────────────────────┐ │ │ └──────────────────────────┘ │ │                │  │
│  │ │         45 POIN          │ │ │ *5 Poin lagi untuk klaim!*   │ │ [ Share to IG ]│  │
│  │ └──────────────────────────┘ │ └──────────────────────────────┘ │ [ Share to X ] │  │
│  │                              │                                  └────────────────┘  │
│  │     [ 🔎 Tunjukkan QR ID ]   │                                                      │
│  │                                                                                     │
│  └──────────────────────────────┘                                                      │
│                                                                                        │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### C. Tampilan Tab Katalog & Menu (dengan Slide-Over Cart terbuka)
Pada Desktop, katalog menu menampilkan produk dalam **4 Kolom Grid**. Ketika tombol keranjang `[🛒 2]` di navbar diklik, **Cart Drawer (Slide-Over)** akan muncul dari sisi kanan layar overlay tanpa menutup daftar produk di sebelah kiri.

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [☕] NGOLAB EXPRESS CAFE  [Beranda]  [*Katalog*]  [Reward]  [Riwayat]  [Profil]    [🛒 2] │
├──────────────────────────────────────────────────────────────────────────┬─────────────┤
│                                                                          │ 🛒 CART     │
│  Pilihan Menu:  ┌──────────────────────────────┐                        │ ─────────── │
│                 │  [*Ngo+Lab Cafe*]  [ Bakso ] │                        │ 2 Items     │
│                 └──────────────────────────────┘                        │             │
│                                                                          │ 1. Kopi Susu│
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐                 │  2x (30k)   │
│  │ [ Gambar ]│ │ [ Gambar ]│ │ [ Gambar ]│ │ [ Gambar ]│                 │             │
│  │ Kopi Aren │ │ Espresso  │ │ Capuccino │ │ Americano │                 │ 2. Bakso T  │
│  │ Rp 15.000 │ │ Rp 10.000 │ │ Rp 18.000 │ │ Rp 12.000 │                 │  1x (20k)   │
│  │ (+1.5P)   │ │ (+1P)     │ │ (+1.8P)   │ │ (+1.2P)   │                 │ ─────────── │
│  │           │ │           │ │           │ │           │                 │ Catatan:    │
│  │ [ +Cart ] │ │ [ +Cart ] │ │ [ +Cart ] │ │ [ +Cart ] │                 │ [less sugar]│
│  └───────────┘ └───────────┘ └───────────┘ └───────────┘                 │ ─────────── │
│                                                                          │ Total: 50k  │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐                 │ Poin: +5P   │
│  │ [ Gambar ]│ │ [ Gambar ]│ │ [ Gambar ]│ │ [ Gambar ]│                 │             │
│  │ Matcha    │ │ Latte     │ │ V60       │ │ Robusta   │                 │ [ 🔥 PESAN ]│
│  │ Rp 20.000 │ │ Rp 15.000 │ │ Rp 16.000 │ │ Rp 8.000  │                 │ [ Tutup ]   │
│  │ (+2P)     │ │ (+1.5P)   │ │ (+1.6P)   │ │ (+0.8P)   │                 │             │
│  │           │ │           │ │           │ │           │                 │             │
│  │ [ +Cart ] │ │ [ +Cart ] │ │ [ +Cart ] │ │ [ +Cart ] │                 │             │
│  └───────────┘ └───────────┘ └───────────┘ └───────────┘                 │             │
└──────────────────────────────────────────────────────────────────────────┴─────────────┘
```

---

### D. Tampilan Tab Katalog Reward (Toko Penukaran Poin)
Tab tersendiri untuk penukaran poin member. Menampilkan hadiah dalam grid kartu, lengkap dengan harga poin, stok, serta tier level yang dibutuhkan.

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [☕] NGOLAB EXPRESS CAFE  [Beranda]  [Katalog]  [*Reward*]  [Riwayat]  [Profil]    [🛒 2] │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│  TOKO PENUKARAN REWARD (SALDO ANDA: 45 POIN)                                           │
│                                                                                        │
│  ┌──────────────────────────────┐ ┌──────────────────────────────┐ ┌────────────────┐  │
│  │ 📡 Voucher WiFi VIP 24 Jam   │ │ ☕ Kopi Susu Aren Gratis     │ │ 🍜 Bakso Urat  │  │
│  │                              │ │                              │ │                │  │
│  │ Poin dibutuhkan: 5 Poin      │ │ Poin dibutuhkan: 15 Poin     │ │ Poin: 25 Poin  │  │
│  │ Stok: 99                     │ │ Stok: 12                     │ │ Stok: 8        │  │
│  │                              │ │                              │ │                │  │
│  │ [ TUKAR POIN ]               │ │ [ TUKAR POIN ]               │ │ [ TUKAR POIN ] │  │
│  └──────────────────────────────┘ └──────────────────────────────┘ └────────────────┘  │
│                                                                                        │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### E. Tampilan Tab Profil & Riwayat Penukaran
Pada Desktop, data Edit Profil Pengguna dan Feedback / Ulasan ditampilkan berdampingan dalam **2 Kolom** lebar. Tab Riwayat sekarang fokus menampilkan timeline status pesanan.

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [☕] NGOLAB EXPRESS CAFE  [Beranda]  [Katalog]  [Reward]  [Riwayat]  [*Profil*]    [🛒 2] │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│  PENGATURAN PROFIL & FEEDBACK                                                          │
│                                                                                        │
│  ┌─────────────────────────────────────────┐ ┌──────────────────────────────────────┐  │
│  │ 👤 EDIT PROFIL MEMBER                   │ │ 💬 KIRIM ULASAN & FEEDBACK           │  │
│  │                                         │ │                                      │  │
│  │ Foto Profil:                            │ │ Rating: [ ★ ★ ★ ★ ★ ]                │  │
│  │ ┌───┐                                   │ │                                      │  │
│  │ │🎴 │ [ Ganti Foto ]                    │ │ Ulasan Anda:                         │  │
│  │ └───┘                                   │ │ ┌──────────────────────────────────┐ │  │
│  │                                         │ │ │ Makanannya enak & pelayanannya   │ │  │
│  │ Nama Lengkap:                           │ │ │ sangat cepat!                    │ │  │
│  │ ┌─────────────────────────────────────┐ │ │ └──────────────────────────────────┘ │  │
│  │ │ Budi Raharjo                        │ │ │                                      │  │
│  │ └─────────────────────────────────────┘ │ │ [ Kirim Ulasan & Klaim Poin (+5P) ]  │  │
│  │ NIM  : 1202210045                       │ └──────────────────────────────────────┘  │
│  │ Key  : ng-a8b27c9d...                   │                                           │
│  │ [ Simpan Perubahan ]                    │                                           │
│  └─────────────────────────────────────────┘                                           │
│                                                                                        │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 📱 Transformasi Responsif (Mobile / Tablet View)

Layout di atas otomatis bertransisi menggunakan media queries CSS (`@media (max-width: 768px)`) untuk menyesuaikan dengan layar kecil:

1. **Perubahan Navigasi:** 
   *   **Desktop:** Top Navbar dengan 5 link.
   *   **Mobile:** Navigasi utama berpindah ke **Bottom Navigation Bar** dengan 5 tombol: `[🏡 Home] [🍔 Menu] [🎁 Reward] [🕒 Log] [👤 User]`.
2. **Katalog Menu & Katalog Reward:**
   *   **Desktop:** Grid berisi 4 Kolom (Menu) & 3 Kolom (Reward).
   *   **Mobile/Tablet:** Grid menciut menjadi **2 Kolom** (atau 1 kolom untuk ponsel berlayar sangat kecil).
3. **Pemberitahuan & Keranjang:**
   *   Keranjang belanja slide-over dari kanan (Desktop) berubah menjadi **Bottom Sheet (Modal Slide-Up)** yang muncul dari bawah layar dengan lebar penuh (100% viewport width).

---

## 💻 Sisi Kasir & Admin Dashboard (Desktop / Tablet View)

Layout admin tetap menggunakan navigasi **Sidebar** permanen di sisi kiri untuk mempermudah navigasi antar modul CRUD PHP, dengan area kerja kasir POS berformat 2 kolom.

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  [☕] NGOLAB POS ADMIN                                          [ Status: ONLINE ● ]   │
├──────────────────────┬─────────────────────────────────────────────────────────────────┤
│                      │                                                                 │
│  [📂 Dashboard]      │  KASIR POS (MOCK TRANSACTION)          LIVE TRANSACTIONS LOG    │
│                      │  ┌──────────────────────────────────┐ ┌───────────────────────┐ │
│  [👥 Kelola Member]  │  │ NOMOR HP / NIM MEMBER            │ │ [12:05] Budi R        │ │
│                      │  │ ┌──────────────────────────────┐ │ │ 1202210045 (+5 Poin)  │ │
│  [👤 Kelola Staff]   │  │ │ 1202210045                   │ │ │ Status: [Success]     │ │
│                      │  │ └──────────────────────────────┘ │ ├───────────────────────┤ │
│  [🍔 Kelola Menu]    │  │ TOTAL BELANJA (RP)               │ │ [11:58] Ani Wijaya    │ │
│                      │  │ ┌──────────────────────────────┐ │ │ 1202210099 (+2 Poin)  │ │
│  [🏷️ Kelola Kategori]│  │ │ 50000                        │ │ │ Status: [Success]     │ │
│                      │  │ └──────────────────────────────┘ │ ├───────────────────────┤ │
│  [🎁 Kelola Reward]  │  │ Preview Poin: +5 Poin            │ │ [11:42] Dedi Prasetyo │ │
│                      │  │                                  │ │ 1202210111 (+1 Poin)  │ │
│  [🛒 Kelola Pesanan] │  │ [ 🔥 PROSES TRANSAKSI (POIN) ]   │ │ Status: [Success]     │ │
│                      │  └──────────────────────────────────┘ └───────────────────────┘ │
│  [💬 Kelola Feedback]│                                                                 │
│                      │  DAFTAR PESANAN MASUK (PENDING SELF-ORDER)                      │
│  [🔑 Logout]         │  ┌────────────────────────────────────────────────────────────┐ │
│                      │  │ ID   │ Member        │ Items              │ Total │ Action  │ │
│                      │  ├──────┼───────────────┼────────────────────┼───────┼─────────┤ │
│                      │  │ #004 │ Budi Raharjo  │ 2x Kopi, 1x Bakso  │ 50k   │[Selesai]│ │
│                      │  │ #003 │ Ani Wijaya    │ 1x Bakso Urat      │ 22k   │[Selesai]│ │
│                      │  └──────┴───────────────┴────────────────────┴───────┴─────────┘ │
└──────────────────────┴─────────────────────────────────────────────────────────────────┘
```
