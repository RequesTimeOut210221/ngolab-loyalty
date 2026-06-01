# FINAL Blueprint: Mekanisme Perolehan Poin Ngolab Cafe (Update Mock POS)

Dokumen ini menjelaskan rancangan logika otomatisasi bagaimana member Ngolab Express Cafe dapat mengumpulkan poin (Earning Points) tanpa harus memasukkan kode secara manual.

## 1. Empat (4) Jalur Perolehan Poin Otomatis

### A. Pembelian Menu via Self-Order (Jalur Utama)

Aplikasi web ini akan memiliki fitur keranjang/pemesanan sederhana layaknya _e-commerce_.

- **Logika Konsumen:** Pelanggan _login_, melihat Katalog Menu, menekan tombol "Pesan", dan melakukan _checkout_. Sistem menghitung `total_harga` (misal Rp 50.000).

- **Konversi Poin:** Sistem otomatis mengkalkulasi poin (Misal: Setiap kelipatan Rp 10.000 = 1 Poin). Jadi pesanan tersebut menghasilkan 5 Poin.

- **Logika Admin:** Pesanan masuk ke Dashboard Admin (Tabel Pesanan). Saat Admin menekan tombol "Pesanan Selesai / Lunas", _trigger backend_ otomatis menambahkan 5 Poin tersebut ke `saldo_poin` user.


### B. Misi Sosial Media (Share to Earn)

- **Logika:** Terdapat tombol "Bagikan ke Instagram/X" di beranda pengguna. Saat diklik, _frontend_ membuka tab baru ke medsos dan bersamaan memanggil endpoint `/api/share-bonus`.

- **Validasi:** Backend mengecek status `is_shared_sosmed` milik user. Jika belum pernah (`false`), berikan +10 Poin dan ubah status menjadi `true` (Hanya berlaku 1x per akun untuk mencegah eksploitasi).


### C. Welcome Bonus (Jalur Registrasi)

- **Logika:** Saat pengguna baru melakukan proses _Register_ (`POST /api/auth`), `saldo_poin` di database secara otomatis diset ke `10` sebagai hadiah selamat datang.


### D. Aktivitas Ulasan / Feedback (Jalur Interaksi)

- **Logika:** Saat pelanggan mengirimkan ulasan jujur (`POST /api/feedback`), sistem backend otomatis memberikan +5 Poin tambahan sebagai bentuk apresiasi (dibatasi 1x per order atau 1x per minggu).


## 2. Penyesuaian Tugas Tim (Revisi Pembagian CRUD & API)

Karena fitur kode unik dihapus dan diganti dengan Mock POS (Pemesanan), maka tugas Anggota 3 diubah menjadi:

**Anggota 3 (Fokus pada Transaksi Pesanan & Poin):**

- **Tugas CRUD (Full PHP):** 1. CRUD Histori Penukaran Reward (Validasi tukar poin). 2. **CRUD Data Pesanan Kafe (Mock POS)**. Admin melihat daftar pesanan masuk. Saat Admin klik "Update Status -> Selesai", query PHP otomatis mengeksekusi `UPDATE tabel_user SET saldo_poin = saldo_poin + poin_didapat`.

- **Tugas API (Frontend & Vanilla JS):**

1. Endpoint `POST /api/redemptions` (Tukar Poin).

2. **Endpoint `POST /api/checkout`** (Membuat pesanan baru dari menu yang dipilih user).


## 3. Contoh Flow API Checkout (JSON)

**Request dari Frontend Konsumen (Vanilla JS Fetch):**

```
POST /api/checkout.php
Header: x-api-key: "kunci-rahasia-user"
Content-Type: application/json

{
"id_menu": 3,
"jumlah_pesanan": 2,
"total_harga": 40000,
"poin_didapat": 4,
"catatan": "Kopinya less sugar ya kak"
}
```

**Validasi di Backend (PHP Native):**

1. Menerima payload JSON.

2. Melakukan `INSERT INTO tabel_pesanan (id_user, id_menu, jumlah, total_harga, poin_didapat, status) VALUES (...)` dengan status awal `'pending'`.

3. Kirim Response JSON: `{"status": "success", "message": "Pesanan dibuat. 4 Poin akan masuk setelah kasir memproses pesananmu!"}`
