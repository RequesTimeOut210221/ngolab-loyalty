<?php
include '../koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM kategori_menu");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = isset($_POST['nama_kategori']) ? mysqli_real_escape_string($conn, $_POST['nama_kategori']) : '';

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
                $gambar_name = uniqid('cat_', true) . '.webp';
                if (!is_dir('../assets/uploads/categories')) mkdir('../assets/uploads/categories', 0777, true);
                imagewebp($image, '../assets/uploads/categories/' . $gambar_name, 80);
                imagedestroy($image);
            }
        }
    }

    mysqli_query($conn, "INSERT INTO kategori_menu (nama_kategori, gambar) VALUES ('$nama_kategori', '$gambar_name')");
    echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil ditambahkan']);
}
