<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php'; // koneksi ke database

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["success" => false, "message" => "User belum login"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Ambil data JSON dari request
    $input = json_decode(file_get_contents('php://input'), true);
    $reward_id = $input['reward_id'] ?? null;

    if (!$reward_id) {
        echo json_encode(["success" => false, "message" => "Reward ID wajib"]);
        exit;
    }

    // Ambil info reward
    $reward = $db->query("SELECT * FROM rewards WHERE id = $reward_id")->fetch_assoc();
    if (!$reward) {
        echo json_encode(["success" => false, "message" => "Reward tidak ditemukan"]);
        exit;
    }

    // Ambil poin user
    $user = $db->query("SELECT points FROM members WHERE id = $user_id")->fetch_assoc();
    $points = $user['points'];

    if ($points < $reward['cost_points']) {
        echo json_encode(["success" => false, "message" => "Poin tidak cukup"]);
        exit;
    }

    // Kurangi poin & simpan transaksi
    $db->query("UPDATE members SET points = points - {$reward['cost_points']} WHERE id = $user_id");
    $db->query("INSERT INTO redemptions (user_id, reward_id, date, status) VALUES ($user_id, $reward_id, NOW(), 'Approved')");

    // Ambil poin terbaru
    $updated = $db->query("SELECT points FROM members WHERE id = $user_id")->fetch_assoc();

    echo json_encode([
        "success" => true,
        "message" => "Reward berhasil ditukar",
        "remaining_points" => $updated['points']
    ]);
}

elseif ($method === 'GET') {
    $result = $db->query("SELECT r.id, rw.name AS reward_name, r.date, r.status 
                          FROM redemptions r 
                          JOIN rewards rw ON r.reward_id = rw.id 
                          WHERE r.user_id = $user_id 
                          ORDER BY r.date DESC");

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }

    echo json_encode($history);
}

else {
    echo json_encode(["success" => false, "message" => "Method tidak didukung"]);
}
