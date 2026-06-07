<?php
include '../koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Mengambil daftar ulasan publik untuk testimoni
    $result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY id_feedback DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menerima Kiriman Ulasan Baru
    $data = json_decode(file_get_contents('php://input'), true);
    $headers = getallheaders();
    $api_key = $headers['x-api-key'] ?? $headers['X-Api-Key'] ?? '';
    $current_user = null;

    if ($api_key !== '') {
        $stmt = $conn->prepare("SELECT id_user, username FROM TABEL_USER WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $current_user = $stmt->get_result()->fetch_assoc();
    }

    $id_user = isset($data['id_user']) && !empty($data['id_user']) ? (int)$data['id_user'] : ($current_user ? (int)$current_user['id_user'] : 'NULL');
    $nama_user = $current_user ? mysqli_real_escape_string($conn, $current_user['username']) : (isset($data['nama_user']) ? mysqli_real_escape_string($conn, $data['nama_user']) : 'Pelanggan Anonim');
    $rating = (int)$data['rating'];
    $ulasan = mysqli_real_escape_string($conn, $data['ulasan']);

    $query = "INSERT INTO feedback (id_user, nama_user, rating, ulasan) VALUES ($id_user, '$nama_user', $rating, '$ulasan')";

    if (mysqli_query($conn, $query)) {
        if ($id_user !== 'NULL') {
            mysqli_query($conn, "UPDATE TABEL_USER SET saldo_poin = saldo_poin + 5 WHERE id_user = $id_user");
        }

        echo json_encode(['status' => 'success', 'message' => 'Berhasil! Anda mendapatkan +5 Poin tambahan.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ulasan.']);
    }
}
