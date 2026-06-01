# FINAL Entity Relationship Diagram (ERD) - Revisi Mock POS

**Proyek:** Program Loyalti / Membership Ngolab Express Cafe **Fokus Revisi:** Penggantian tabel kode struk menjadi tabel `TABEL_PESANAN` untuk mengakomodasi fitur Self-Order dan injeksi poin otomatis.

## 1. Skema Relasi Antar Tabel

```mermaid
erDiagram
    %% Tabel Pengguna / Auth (Anggota 1)
    TABEL_USER {
        INT id_user PK "Auto Increment"
        VARCHAR username
        VARCHAR email
        VARCHAR password "Hash SHA-256"
        INT saldo_poin "Default 10 (Welcome Bonus)"
        BOOLEAN is_shared_sosmed "Default FALSE (Untuk Misi Share)"
        VARCHAR api_key "Untuk otorisasi endpoint"
    }

    TABEL_STAFF {
        INT id_staff PK "Auto Increment"
        VARCHAR username
        VARCHAR email
        VARCHAR password "Hash SHA-256"
        ENUM role "'admin', 'kasir'"
    }

    %% Tabel Reward & Kategori (Anggota 2)
    TABEL_KATEGORI {
        INT id_kategori PK "Auto Increment"
        VARCHAR nama_kategori
        TEXT deskripsi
    }

    TABEL_REWARD {
        INT id_reward PK "Auto Increment"
        INT id_kategori FK
        VARCHAR nama_reward
        INT poin_dibutuhkan
        INT stok
        VARCHAR gambar
    }

    %% Tabel Transaksi (Anggota 3)
    TABEL_PESANAN {
        INT id_pesanan PK "Auto Increment"
        INT id_user FK
        INT id_menu FK
        INT jumlah
        INT total_harga
        INT poin_didapat "Poin yang akan masuk jika lunas"
        TEXT catatan_pesanan
        ENUM status "'pending', 'diproses', 'selesai'"
        DATETIME tanggal_pesan
    }

    TABEL_PENUKARAN_REWARD {
        INT id_penukaran PK "Auto Increment"
        INT id_user FK
        INT id_reward FK
        DATETIME tanggal_tukar
        ENUM status "'pending', 'berhasil', 'ditolak'"
    }

    %% Tabel Feedback & Menu (Anggota 4)
    TABEL_MENU {
        INT id_menu PK "Auto Increment"
        VARCHAR nama_menu
        INT harga
        VARCHAR gambar_menu
    }

    TABEL_FEEDBACK {
        INT id_feedback PK "Auto Increment"
        INT id_user FK
        INT rating "1-5"
        TEXT ulasan
        BOOLEAN bonus_poin_diklaim "Default TRUE jika poin sudah masuk"
    }

    %% Definisi Relasi
    TABEL_KATEGORI ||--o{ TABEL_REWARD : "memiliki"
    TABEL_USER ||--o{ TABEL_PENUKARAN_REWARD : "menukar_poin_dengan"
    TABEL_REWARD ||--o{ TABEL_PENUKARAN_REWARD : "diklaim_pada"
    TABEL_USER ||--o{ TABEL_FEEDBACK : "memberikan"
    TABEL_USER ||--o{ TABEL_PESANAN : "melakukan_order"
    TABEL_MENU ||--o{ TABEL_PESANAN : "dipesan_pada"
```

## 2. Penjelasan Perubahan Logika (Self-Order)

1. **TABEL_PESANAN (Menggantikan Kode Struk):** Sistem bergeser menjadi _seamless_. Konsumen memilih menu -> pesan -> masuk database.

2. **Pencairan Poin (Trigger):** Poin **tidak langsung bertambah** saat pesanan dibuat (karena bisa saja di-cancel). Poin bertambah ketika Admin di halaman Dashboard melakukan _Update Status_ pada `TABEL_PESANAN` dari `'pending'` menjadi `'selesai'`.

3. **Kolom Baru Misi Sosial Media:** Pada `TABEL_USER`, ditambahkan `is_shared_sosmed`. Frontend cukup memanggil API `/api/share-bonus`, lalu backend memvalidasi dan mengubah _boolean_ ini menjadi `true` sambil menyuntikkan tambahan saldo poin.

4. **Relasi Menu & Pesanan:** Relasi ini membuat fungsionalitas web semakin kaya karena tabel menu (yang diinput oleh anggota 4) kini saling terikat dengan tabel pesanan (yang dikelola anggota 3).
