<?php
header('Content-Type: application/json');
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak didukung.']);
    exit;
}

$result = $conn->query("SELECT * FROM TABEL_REWARD ORDER BY poin_dibutuhkan ASC");
$rewards = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rewards[] = $row;
    }
}

echo json_encode($rewards);
?>
