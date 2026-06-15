<?php
/* Author: Ayesha */

include '../config/koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Mengambil daftar ulasan publik untuk testimoni
    $result = mysqli_query($conn, "SELECT * FROM TABEL_FEEDBACK ORDER BY id_feedback DESC");
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menerima Kiriman Ulasan Baru
    $data = json_decode(file_get_contents('php://input'), true);
    $headers = getallheaders();
    $api_key = $headers['x-api-key'] ?? $headers['X-Api-Key'] ?? '';
    $current_user = null;

    if ($api_key !== '') {
        $stmt = $conn->prepare("SELECT id_member, username FROM TABEL_MEMBER WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $current_user = $stmt->get_result()->fetch_assoc();
    }

    $id_member = isset($data['id_member']) && !empty($data['id_member']) ? (int)$data['id_member'] : ($current_user ? (int)$current_user['id_member'] : 'NULL');
    $nama_user = $current_user ? mysqli_real_escape_string($conn, $current_user['username']) : (isset($data['nama_user']) ? mysqli_real_escape_string($conn, $data['nama_user']) : 'Pelanggan Anonim');
    $rating = (int)$data['rating'];
    $ulasan = mysqli_real_escape_string($conn, $data['ulasan']);

    $query = "INSERT INTO TABEL_FEEDBACK (id_member, nama_user, rating, ulasan) VALUES ($id_member, '$nama_user', $rating, '$ulasan')";

    if (mysqli_query($conn, $query)) {
        if ($id_member !== 'NULL') {
            mysqli_query($conn, "UPDATE TABEL_MEMBER SET saldo_poin = saldo_poin + 10 WHERE id_member = $id_member");
        }

        echo json_encode(['status' => 'success', 'message' => 'Berhasil! Anda mendapatkan +10 Poin tambahan.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ulasan.']);
    }
}
