<?php
header('Content-Type: application/json');
require_once '../koneksi.php';

function current_user(mysqli $conn): ?array {
    $headers = getallheaders();
    $api_key = $headers['x-api-key'] ?? $headers['X-Api-Key'] ?? '';
    if ($api_key === '') return null;

    $stmt = $conn->prepare("SELECT * FROM TABEL_USER WHERE api_key = ?");
    $stmt->bind_param("s", $api_key);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    return $user ?: null;
}

$user = current_user($conn);
if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API Key']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("
        SELECT p.*, m.nama_menu
        FROM TABEL_PESANAN p
        LEFT JOIN menus m ON m.id = p.id_menu
        WHERE p.id_user = ?
        ORDER BY p.tanggal_pesan DESC
    ");
    $stmt->bind_param("i", $user['id_user']);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $id_menu = (int)($data['id_menu'] ?? 0);
    $jumlah = (int)($data['jumlah_pesanan'] ?? $data['jumlah'] ?? 1);
    $total_harga = (int)($data['total_harga'] ?? 0);
    $poin_didapat = (int)($data['poin_didapat'] ?? floor($total_harga / 10000));
    $catatan = $data['catatan'] ?? '';

    if ($id_menu <= 0 || $jumlah <= 0 || $total_harga <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data pesanan tidak lengkap.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO TABEL_PESANAN (id_user, id_menu, jumlah, total_harga, poin_didapat, catatan_pesanan, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iiiiis", $user['id_user'], $id_menu, $jumlah, $total_harga, $poin_didapat, $catatan);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Pesanan pending dibuat.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat pesanan: ' . $stmt->error]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Method tidak didukung.']);
?>
