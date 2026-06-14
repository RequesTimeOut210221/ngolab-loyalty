<?php
/* Author: Shaena */

header('Content-Type: application/json');
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak didukung.']);
    exit;
}

$query = "
    SELECT r.*, k.nama_kategori 
    FROM TABEL_REWARD r
    LEFT JOIN TABEL_KATEGORI_REWARD k ON r.id_kategori = k.id_kategori
    ORDER BY r.poin_dibutuhkan ASC
";

$result = $conn->query($query);
$rewards = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rewards[] = $row;
    }
}

echo json_encode($rewards);
?>
