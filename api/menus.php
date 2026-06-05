<?php
include '../koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get Menu dengan Filter Kategori
    $kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
    $query = "SELECT * FROM menus";
    if ($kategori) {
        $query .= " WHERE kategori = '$kategori'";
    }

    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST secara terprogram
    $data = json_decode(file_get_contents('php://input'), true);
    $nama = mysqli_real_escape_string($conn, $data['nama_menu']);
    $harga = (int)$data['harga'];
    $kategori = mysqli_real_escape_string($conn, $data['kategori']);
    $gambar = isset($data['gambar']) ? mysqli_real_escape_string($conn, $data['gambar']) : '';
    $deskripsi = isset($data['deskripsi']) ? mysqli_real_escape_string($conn, $data['deskripsi']) : '';
    $poin_didapat = floor($harga / 10000); // Hitung poin otomatis

    mysqli_query($conn, "INSERT INTO menus (nama_menu, harga, kategori, deskripsi, poin_didapat, gambar, is_promo) VALUES ('$nama', '$harga', '$kategori', '$deskripsi', '$poin_didapat', '$gambar', 0)");
    echo json_encode(['status' => 'success', 'message' => 'Berhasil ditambahkan melalui API']);
}
