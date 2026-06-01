# Cetak Biru Layout (Mobile-First) - Ngolab Express Cafe

Dokumen ini berisi representasi tata letak (layout) awal berbasis **Mobile-First / PWA** menggunakan ASCII Art untuk aplikasi loyalitas dan pemesanan mandiri **Ngolab Express Cafe**. Rancangan ini ditargetkan untuk layar ponsel guna mempercepat loading dan mempermudah akses jempol pengguna.

---

## 📱 Sisi Konsumen (Single Page Application - `index.html`)

Halaman utama konsumen berbasis **Single Page Application (SPA)** dengan navigasi diatur menggunakan **Bottom Navigation Bar** untuk berpindah antar 5 tab secara dinamis via JavaScript (`assets/js/app.js`).

### A. Tab Beranda (Home Tab Layout)
Menampilkan Kartu ID Member digital, level tier, jumlah poin saat ini, indikator progress hadiah berikutnya, dan menu akses cepat.

```text
┌──────────────────────────────────────────┐
│  [☕] NGOLAB CAFE & BAKSO                │
├──────────────────────────────────────────┤
│                                          │
│  ┌────────────────────────────────────┐  │
│  │  CARD GRADIENT (Silver Tier)        │  │ <-- Warna gradasi sesuai Tier
│  │                                    │  │
│  │  Nama: Budi Raharjo                │  │
│  │  NIM : 1202210045                  │  │
│  │                                    │  │
│  │  Saldo Poin:                       │  │
│  │  ┌──────────────────────────────┐  │  │
│  │  │         45 POIN              │  │  │ <-- Font ukuran terbesar (H1)
│  │  └──────────────────────────────┘  │  │
│  │                                    │  │
│  │        [ 🔎 Tunjukkan QR ID ]      │  │ <-- Tombol buka QR/Barcode Modal
│  └────────────────────────────────────┘  │
│                                          │
│  ┌────────────────────────────────────┐  │
│  │  🎯 Target Reward Berikutnya        │  │
│  │  ┌──────────────────────────────┐  │  │
│  │  │███████████████░░░░░░░░░░░░░░  │  │  │ <-- Progress bar (Gamifikasi)
│  │  └──────────────────────────────┘  │  │
│  │  *5 Poin lagi untuk Kopi Susu Gratis*│  │
│  └────────────────────────────────────┘  │
│                                          │
│  ┌────────────────────────────────────┐  │
│  │  ⚡ Akses Cepat                     │  │
│  │  ┌──────────────────────────────┐  │  │
│  │  │ [ Claim WiFi VIP (5 Poin) ]   │  │  │ <-- Instantly redeem & open modal
│  │  └──────────────────────────────┘  │  │
│  └────────────────────────────────────┘  │
│                                          │
├──────────────────────────────────────────┤
│  [🏡 Home] [🍔 Menu] [🎁 Reward] [🕒 Log] [👤 User]│ <-- Bottom Nav (Active: Home)
└──────────────────────────────────────────┘
```

---

### B. Tab Katalog & Menu (Catalog Tab Layout)
Menampilkan pilihan menu kopi kafe vs bakso tradisional menggunakan segmented control switcher. Terdapat tombol keranjang belanja melayang (floating cart badge).

```text
┌──────────────────────────────────────────┐
│  [☕] KATALOG MENU                     [🛒]│ <-- Floating Cart (Qty: 2) -> Klik untuk
├──────────────────────────────────────────┤     membuka Drawer/Bottom Sheet
│  ┌────────────────────────────────────┐  │
│  │   [*Ngo+Lab Cafe*]  [ Bakso Yanto ]│  │ <-- Segmented Toggle Switcher
│  └────────────────────────────────────┘  │
│                                          │
│  ┌──────────────────┐┌──────────────────┐│
│  │ ┌──────────────┐ ││ ┌──────────────┐ ││
│  │ │  [ Gambar ]  │ ││ │  [ Gambar ]  │ ││ <-- Rounded-t Image
│  │ └──────────────┘ ││ └──────────────┘ ││
│  │ Kopi Susu Aren   ││ Espresso         ││
│  │ Rp 15.000        ││ Rp 10.000        ││
│  │ (+1.5 Poin)      ││ (+1 Poin)        ││
│  │                  ││                  ││
│  │ [ + Keranjang ]  ││ [ + Keranjang ]  ││ <-- Add to cart action target
│  └──────────────────┘└──────────────────┘│
│  ┌──────────────────┐┌──────────────────┐│
│  │ ┌──────────────┐ ││ ┌──────────────┐ ││
│  │ │  [ Gambar ]  │ ││ │  [ Gambar ]  │ ││
│  │ └──────────────┘ ││ └──────────────┘ ││
│  │ Caramel Machiato ││ Americano        ││
│  │ Rp 22.000        ││ Rp 12.000        ││
│  │ (+2.2 Poin)      ││ (+1.2 Poin)      ││
│  │                  ││                  ││
│  │ [ + Keranjang ]  ││ [ + Keranjang ]  ││
│  └──────────────────┘└──────────────────┘│
├──────────────────────────────────────────┤
│  [🏡 Home] [🍔 Menu] [🎁 Reward] [🕒 Log] [👤 User]│ <-- Bottom Nav (Active: Menu)
└──────────────────────────────────────────┘
```

---

### C. Tab Katalog Reward (Toko Penukaran Poin)
Menampilkan item hadiah yang bisa ditukar oleh member menggunakan poin mereka.

```text
┌──────────────────────────────────────────┐
│  [🎁] KATALOG REWARD                     │
├──────────────────────────────────────────┤
│  Saldo Poin Anda: 45 POIN                │
│                                          │
│  ┌────────────────────────────────────┐  │
│  │ 📡 WiFi VIP Voucher 24 Jam         │  │
│  │ Poin: 5 Poin | Stok: 99            │  │
│  │ [ TUKAR POIN ]                     │  │
│  └────────────────────────────────────┘  │
│  ┌────────────────────────────────────┐  │
│  │ ☕ Kopi Susu Aren Gratis            │  │
│  │ Poin: 15 Poin | Stok: 12           │  │
│  │ [ TUKAR POIN ]                     │  │
│  └────────────────────────────────────┘  │
│  ┌────────────────────────────────────┐  │
│  │ 🍜 Bakso Urat Jumbo Gratis         │  │
│  │ Poin: 25 Poin | Stok: 8            │  │
│  │ [ TUKAR POIN ]                     │  │
│  └────────────────────────────────────┘  │
├──────────────────────────────────────────┤
│  [🏡 Home] [🍔 Menu] [🎁 Reward] [🕒 Log] [👤 User]│ <-- Bottom Nav (Active: Reward)
└──────────────────────────────────────────┘
```

---

### D. Drawer Keranjang Belanja & Checkout (Bottom Sheet Layout)
Muncul sebagai bottom sheet slide-up ketika tombol keranjang `[🛒]` ditekan.

```text
┌──────────────────────────────────────────┐
│  [☕] KATALOG MENU                     [🛒]│
├──────────────────────────────────────────┤
│                                          │
│   ====================================   │
│   ▲   KERANJANG BELANJA (Checkout)   ▲   │ <-- Slide up overlay / Bottom Sheet
│   ====================================   │
│   Detail Pesanan:                        │
│   1. Kopi Susu Aren (2x)   - Rp 30.000   │
│   2. Bakso Telur (1x)      - Rp 20.000   │
│   ------------------------------------   │
│   Catatan Pesanan:                       │
│   [ Less sugar, bakso pisah kuah...  ]   │ <-- Input catatan konsumen
│   ------------------------------------   │
│   Total Belanja  : Rp 50.000             │
│   Potensi Poin   : +5 Poin               │ <-- Kalkulasi otomatis Rp 10.000 = 1 Poin
│   ------------------------------------   │
│                                          │
│   [  🔥 Pesan Sekarang (Self-Order)  ]   │ <-- Menembak api/checkout.php
│   [             Tutup                ]   │
│                                          │
├──────────────────────────────────────────┤
│  [🏡 Home] [🍔 Menu] [🎁 Reward] [🕒 Log] [👤 User]│
└──────────────────────────────────────────┘
```

---

### E. Tab Profil, Share Bonus, & Feedback (Profile Tab Layout)
Menampilkan form edit profil member, unggah foto profil (avatar), detail API Key untuk kebutuhan asesmen, serta form feedback ulasan.

```text
┌──────────────────────────────────────────┐
│  [👤] PROFIL & FEEDBACK                  │
├──────────────────────────────────────────┤
│  ┌───┐                                   │
│  │🎴 │ Budi Raharjo                      │ <-- Preview Foto Profil/Avatar
│  └───┘ [ Ganti Foto ]                    │ <-- Input file upload
│  NIM: 1202210045 | API-Key: ng-a8b27c... │
│  ┌────────────────────────────────────┐  │
│  │ Ubah Nama: [ Budi Raharjo        ] │  │
│  │ [ Simpan Perubahan ]               │  │ <-- Kirim PUT ke api/profile.php
│  └────────────────────────────────────┘  │
│                                          │
│  ┌────────────────────────────────────┐  │
│  │ 🎁 MISI MEDSOS (Dapatkan +10P)     │  │
│  │ [ Share ke Instagram / X ]         │  │ <-- Pemicu api/share_bonus.php (1x)
│  └────────────────────────────────────┘  │
│                                          │
│  ┌────────────────────────────────────┐  │
│  │ 💬 KIRIM FEEDBACK & RATING         │  │
│  │ Rating: [ ★ ★ ★ ★ ★ ]              │  │
│  │ [ Kirim Ulasan & Klaim +5 Poin ]   │  │
│  └────────────────────────────────────┘  │
├──────────────────────────────────────────┤
│  [🏡 Home] [🍔 Menu] [🎁 Reward] [🕒 Log] [👤 User]│ <-- Bottom Nav (Active: User)
└──────────────────────────────────────────┘
```

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
