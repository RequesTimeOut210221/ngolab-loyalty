<?php
include __DIR__ . '/../config/koneksi.php';
$res = mysqli_query($conn, "SELECT m.*, k.nama_kategori as kategori FROM TABEL_MENU m LEFT JOIN TABEL_KATEGORI_MENU k ON m.id_kategori = k.id_kategori");
$data = [];
while ($row = mysqli_fetch_assoc($res)) { $data[] = $row; }
print_r($data);
