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
    $nama = isset($_POST['nama_menu']) ? mysqli_real_escape_string($conn, $_POST['nama_menu']) : '';
    $harga = isset($_POST['harga']) ? (int)$_POST['harga'] : 0;
    $kategori = isset($_POST['kategori']) ? mysqli_real_escape_string($conn, $_POST['kategori']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, $_POST['deskripsi']) : '';
    $poin_didapat = floor($harga / 10000); // Hitung poin otomatis

    $gambar_name = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($fileType, $allowedTypes)) {
            switch ($fileType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fileTmpPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($fileTmpPath);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($fileTmpPath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($fileTmpPath);
                    break;
            }
            if (isset($image)) {
                $gambar_name = uniqid('img_', true) . '.webp';
                imagewebp($image, '../assets/uploads/menus/' . $gambar_name, 80);
                imagedestroy($image);
            }
        }
    }

    mysqli_query($conn, "INSERT INTO menus (nama_menu, harga, kategori, deskripsi, poin_didapat, gambar, is_promo) VALUES ('$nama', '$harga', '$kategori', '$deskripsi', '$poin_didapat', '$gambar_name', 0)");
    echo json_encode(['status' => 'success', 'message' => 'Berhasil ditambahkan melalui API', 'gambar' => $gambar_name]);
}
