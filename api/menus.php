<?php
/* Author: Ayesha */

include '../config/koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get Menu dengan Filter Kategori
    $kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
    
    $query = "SELECT m.*, k.nama_kategori as kategori 
              FROM TABEL_MENU m
              LEFT JOIN TABEL_KATEGORI_MENU k ON m.id_kategori = k.id_kategori";
              
    if ($kategori) {
        $query .= " WHERE LOWER(k.nama_kategori) = LOWER('$kategori')";
    }

    $result = mysqli_query($conn, $query);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST secara terprogram
    $data = json_decode(file_get_contents('php://input'), true);
    $nama = mysqli_real_escape_string($conn, $data['nama_menu']);
    $harga = (int)$data['harga'];
    $id_kategori = (int)$data['id_kategori'];
    $gambar = isset($data['gambar']) ? mysqli_real_escape_string($conn, $data['gambar']) : '';
    $deskripsi = isset($data['deskripsi']) ? mysqli_real_escape_string($conn, $data['deskripsi']) : '';
    $poin_didapat = floor($harga / 10000); 

    mysqli_query($conn, "INSERT INTO TABEL_MENU (nama_menu, harga, id_kategori, deskripsi, poin_didapat, gambar, is_promo) VALUES ('$nama', '$harga', $id_kategori, '$deskripsi', '$poin_didapat', '$gambar', 0)");
    echo json_encode(['status' => 'success', 'message' => 'Berhasil ditambahkan melalui API']);
}
?>
