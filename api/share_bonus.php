<?php
/* Author: Shaena */

header('Content-Type: application/json');
require_once '../config/koneksi.php';

$headers = getallheaders();
$api_key = $headers['x-api-key'] ?? $headers['X-Api-Key'] ?? '';

if ($api_key === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing x-api-key header']);
    exit;
}

$stmt = $conn->prepare("SELECT id_member, is_shared_sosmed, saldo_poin FROM TABEL_MEMBER WHERE api_key = ?");
$stmt->bind_param("s", $api_key);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API Key']);
    exit;
}

if ((int)$user['is_shared_sosmed'] === 1) {
    echo json_encode(['status' => 'error', 'message' => 'Bonus sudah pernah diklaim']);
    exit;
}

$stmt = $conn->prepare("UPDATE TABEL_MEMBER SET saldo_poin = saldo_poin + 10, is_shared_sosmed = 1 WHERE id_member = ?");
$stmt->bind_param("i", $user['id_member']);
$stmt->execute();

$current_points = (int)$user['saldo_poin'] + 10;
echo json_encode([
    'status' => 'success',
    'message' => '+10 poin berhasil ditambahkan',
    'current_points' => $current_points
]);
?>
