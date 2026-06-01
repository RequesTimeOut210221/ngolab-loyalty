UI/UX ARCHITECTURE BLUEPRINT: Ngolab Member App

Project: Unified Loyalty Ecosystem (Ngo+Lab Cafe & Bakso Mas Yanto)
Methodology: User-Centered Design (UCD)
Target Engine: Google Stitch / AI-Assisted UI Generator
Tech Stack Target: React / Next.js / Tailwind CSS

1. DESIGN SYSTEM & TOKENS

Sistem visual harus mencerminkan ekosistem hybrid (Modern Cafe + Tradisional Warung) namun tetap mempertahankan UI yang minimalis agar loading super cepat.

1.1 Color Palette

Primary (Brand - Bakso/Energetic): bg-orange-500 (Hex: #F97316) - Digunakan untuk tombol utama (Call to Action).

Secondary (Brand - Cafe/Modern): bg-slate-800 (Hex: #1E293B) - Digunakan untuk Header, Sidebar, dan teks penekanan.

Background (Canvas): bg-gray-50 (Hex: #F9FAFB) - Untuk mengurangi kelelahan mata.

Success (Atomic Transaction): bg-green-500 - Khusus untuk notifikasi "Poin Berhasil Ditambah".

Surface/Cards: bg-white dengan bayangan ringan shadow-sm rounded-xl.

1.2 Typography

Font Family: Inter atau Roboto (Sans-serif, tingkat keterbacaan tinggi).

Scale (8-Point Grid Rule):

H1 (Header/Saldo): text-3xl font-bold

H2 (Section Title): text-xl font-semibold

Body (Deskripsi): text-sm text-gray-600

2. ADMIN DASHBOARD UI (Sisi Kasir)

User Persona Target: Si Paling Gercep & Staff Kasir
UX Goal: Proses input < 3 detik. No-friction transaction.

2.1 Layout Structure (Tablet/Desktop Focus)

Layout: 2-Column Split (Kiri: Input Interaksi, Kanan: Live Log Transaksi).

Navigation: Top bar minimalis dengan tulisan "Ngolab POS Admin" dan Indikator Status Server (Online/Offline).

2.2 Core Components (Left Column - The Engine)

Fast Identity Input (Hero Section):

Kolom input teks besar (h-16 text-2xl).

Placeholder: "Input Nomor HP / NIM..."

Auto-focus saat halaman dimuat agar kasir bisa langsung mengetik atau menggunakan barcode scanner.

Amount Calculator:

Input nominal rupiah: "Total Belanja (Rp)".

AI Trigger: Saat nominal diketik, sistem otomatis menampilkan (Preview: +X Poin) di bawah kolom.

Action Buttons (Massive Click Target):

Tombol "Proses Poin" (w-full bg-orange-500 text-white p-4 rounded-xl text-lg font-bold).

2.3 Core Components (Right Column - The Audit)

Real-time Transaction Log:

Daftar list vertikal (Card list).

Menampilkan: [Waktu] - [Nama/NIM Member] - [+ Poin] - [Status: Success].

Desain: Minimalis dengan badge hijau untuk sukses.

3. MEMBER DASHBOARD UI (Sisi Pelanggan)

User Persona Target: Si Paling Nugas & Si Paling Diskon
UX Goal: Transparansi saldo, navigasi ringan, gamifikasi (progress bar).

3.1 Layout Structure (Mobile-First / PWA Focus)

Layout: Single column, scrolling vertikal.

Navigation: Bottom Navigation Bar (Tab: Home, Katalog, Riwayat, Profil).

3.2 Core Components (Home Tab)

Digital ID Card (Hero Section):

Kartu digital dengan gradasi warna sesuai Tier (Bronze/Silver/Gold).

Menampilkan Saldo Poin saat ini dengan ukuran Font terbesar.

Terdapat tombol QR Code kecil untuk "Tunjukkan ID ke Kasir".

Next Reward Progress (Gamification):

Progress bar horizontal.

Teks: "5 Poin lagi untuk Kopi Susu Gratis!"

Quick Claim Voucher (Untuk Si Paling Nugas):

Satu tombol khusus di bawah saldo: "Klaim WiFi VIP (Tukar 5 Poin)".

Interaction: Jika diklik, langsung memunculkan Modal/Popup berisi kode token WiFi.

3.3 Core Components (Catalog Tab - Hybrid Menu)

Segmented Control / Tabs:

Dua tombol Toggle di atas: [Menu Ngo+Lab Cafe] | [Menu Bakso Mas Yanto].

Reward Grid:

Grid 2 kolom berisi kartu menu.

Isi Kartu: Foto Menu (Rounded-t), Nama Menu, Label Harga Poin (Contoh: 15 Poin), Tombol "Tukar".

4. INTERACTION & STATE BEHAVIOR

Instruksi khusus untuk AI Generator mengenai interaksi elemen:

Kasir Input State: Jika nomor HP yang dimasukkan tidak ada di database, ubah warna border input menjadi border-yellow-500 dan munculkan tombol sekunder: "Daftarkan Member Baru".

Loading State: Gunakan Skeleton Loading (blok abu-abu berkedip) saat memuat Katalog Hadiah, jangan gunakan layar putih kosong (untuk menjaga retention).

Atomic Feedback: Setiap penukaran poin oleh Member harus memunculkan Toast Notification di atas layar: "Berhasil! 15 Poin ditukar dengan Bakso Spesial. Tunjukkan kasir!"

// END OF DESIGN BLUEPRINT
