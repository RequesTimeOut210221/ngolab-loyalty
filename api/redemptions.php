<?php
/* Author: Shaena */

header('Content-Type: application/json');
require_once '../koneksi.php';

function current_user(mysqli $conn): ?array {
    $headers = getallheaders();
    $api_key = $headers['x-api-key'] ?? $headers['X-Api-Key'] ?? '';
    if ($api_key === '') return null;

    $stmt = $conn->prepare("SELECT * FROM TABEL_MEMBER WHERE api_key = ?");
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
        SELECT p.*, r.nama_reward, r.poin_dibutuhkan
        FROM TABEL_PENUKARAN_REWARD p
        JOIN TABEL_REWARD r ON r.id_reward = p.id_reward
        WHERE p.id_member = ?
        ORDER BY p.tanggal_tukar DESC
    ");
    $stmt->bind_param("i", $user['id_member']);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    echo json_encode($history);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $id_reward = (int)($data['id_reward'] ?? $data['reward_id'] ?? 0);

    $stmt = $conn->prepare("SELECT * FROM TABEL_REWARD WHERE id_reward = ?");
    $stmt->bind_param("i", $id_reward);
    $stmt->execute();
    $reward = $stmt->get_result()->fetch_assoc();

    if (!$reward) {
        echo json_encode(['status' => 'error', 'message' => 'Reward tidak ditemukan.']);
        exit;
    }

    if ((int)$user['saldo_poin'] < (int)$reward['poin_dibutuhkan']) {
        echo json_encode(['status' => 'error', 'message' => 'Poin tidak cukup.']);
        exit;
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE TABEL_MEMBER SET saldo_poin = saldo_poin - ? WHERE id_member = ?");
        $stmt->bind_param("ii", $reward['poin_dibutuhkan'], $user['id_member']);
        $stmt->execute();

        $status = ((int)$id_reward === 1) ? 'berhasil' : 'pending';
        $token_wifi = ((int)$id_reward === 1) ? 'WIFI-VIP-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6)) : null;
        $stmt = $conn->prepare("INSERT INTO TABEL_PENUKARAN_REWARD (id_member, id_reward, status, token_wifi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user['id_member'], $id_reward, $status, $token_wifi);
        $stmt->execute();

        $conn->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Reward berhasil ditukar.',
            'token_wifi' => $token_wifi,
            'current_points' => (int)$user['saldo_poin'] - (int)$reward['poin_dibutuhkan']
        ]);
    } catch (Throwable $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Gagal menukar reward: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Method tidak didukung.']);
?>
