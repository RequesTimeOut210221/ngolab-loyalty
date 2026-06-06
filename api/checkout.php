<?php
header('Content-Type: application/json');

// 1. KONEKSI DATABASE
$host = "localhost";
$username = "root";
$password = "";
$dbname = "ngolab"; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal: " . $conn->connect_error
    ]);
    exit();
}

// Ambil metode request (GET atau POST)
$method = $_SERVER['REQUEST_METHOD'];

// =========================================================================
// [GET] UNTUK MENAMPILKAN DATA (DIPANGGIL SAAT HALAMAN DI-LOAD)
// =========================================================================
if ($method === 'GET') {
    // Query untuk mengambil semua data pesanan, diurutkan dari yang terbaru
    $sql = "SELECT id_pesanan, tanggal, nama_member, nim_member, no_hp, total_belanja, poin_didapat, status 
            FROM pesanan 
            ORDER BY tanggal DESC";
            
    $result = $conn->query($sql);

    if ($result) {
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        echo json_encode([
            "status" => "success",
            "data" => $orders
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal mengambil data dari database: " . $conn->error
        ]);
    }
}

// =========================================================================
// [POST] UNTUK MENGUBAH STATUS (DIPANGGIL SAAT KLIK TOMBOL PROSES/SELESAI/BATAL)
// =========================================================================
if ($method === 'POST') {
    // Ambil data JSON yang dikirim oleh Javascript
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'update_status') {
        $id_pesanan = $conn->real_escape_string($data['id_pesanan']);
        $status_baru = $conn->real_escape_string($data['status']);

        // 1. Update status pesanan di tabel pesanan
        $sql_update = "UPDATE pesanan SET status = '$status_baru' WHERE id_pesanan = '$id_pesanan'";
        
        if ($conn->query($sql_update)) {
            
            // 2. Jika status diubah ke 'Selesai', otomatis tambahkan poin ke saldo users
            if ($status_baru === 'Selesai') {
                // Ambil data NIM dan poin dari pesanan tersebut
                $sql_get_order = "SELECT nim_member, poin_didapat FROM pesanan WHERE id_pesanan = '$id_pesanan'";
                $order_res = $conn->query($sql_get_order);
                
                if ($order_res && $order_res->num_rows > 0) {
                    $order_data = $order_res->fetch_assoc();
                    $nim = $conn->real_escape_string($order_data['nim_member']);
                    $poin = intval($order_data['poin_didapat']);
                    
                    // Note: Nama tabel diubah ke 'users' & kolom disesuaikan biar gak error
                    if (!empty($nim) && $poin > 0) {
                        $sql_update_poin = "UPDATE users SET saldo_poin = saldo_poin + $poin WHERE nim_member = '$nim'";
                        $conn->query($sql_update_poin);
                    }
                }
            }

            echo json_encode([
                "status" => "success",
                "message" => "Status pesanan berhasil diperbarui ke '$status_baru'"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal memperbarui status: " . $conn->error
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Aksi tidak valid atau data tidak lengkap."
        ]);
    }
}

$conn->close();
?>