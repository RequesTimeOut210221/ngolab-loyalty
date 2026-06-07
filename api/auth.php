<?php
header('Content-Type: application/json');
require_once '../koneksi.php';

$action = $_GET['action'] ?? '';

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($action === 'login') {
    $identifier = $data['phone'] ?? '';
    $password = $data['password'] ?? '';
    
    $hashed_password = hash('sha256', $password);

    // Check TABEL_USER first
    $stmt = $conn->prepare("SELECT * FROM TABEL_USER WHERE no_hp = ? OR nim = ? OR username = ?");
    $stmt->bind_param("sss", $identifier, $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($hashed_password === $user['password']) {
            echo json_encode([
                'status' => 'success',
                'role' => 'member',
                'key' => $user['api_key'],
                'user' => [
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'nim' => $user['nim'],
                    'no_hp' => $user['no_hp'],
                    'saldo_poin' => (int)$user['saldo_poin'],
                    'is_shared_sosmed' => (bool)$user['is_shared_sosmed'],
                    'avatar' => $user['avatar']
                ]
            ]);
            exit;
        }
    }
    
    // Check TABEL_STAFF
    $stmt = $conn->prepare("SELECT * FROM TABEL_STAFF WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $staff = $result->fetch_assoc();
        if ($hashed_password === $staff['password']) {
            // Login as admin, we can set session here if needed for PHP admin pages
            session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $staff['username'];
            $_SESSION['admin_role'] = $staff['role'];
            
            echo json_encode([
                'status' => 'success',
                'role' => 'admin',
                'redirect' => 'admin/kelola_member.php',
                'message' => 'Welcome Admin'
            ]);
            exit;
        }
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Kredensial salah!']);
    
} elseif ($action === 'register') {
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $password = hash('sha256', $data['password'] ?? '');
    
    // Cek apakah sudah ada
    $check = $conn->prepare("SELECT id_user FROM TABEL_USER WHERE no_hp = ? OR email = ?");
    $check->bind_param("ss", $phone, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email atau Nomor HP sudah terdaftar.']);
        exit;
    }
    
    $api_key = 'ng-key-' . bin2hex(random_bytes(16));
    
    $stmt = $conn->prepare("INSERT INTO TABEL_USER (username, email, password, no_hp, api_key, saldo_poin) VALUES (?, ?, ?, ?, ?, 10)");
    $stmt->bind_param("sssss", $username, $email, $password, $phone, $api_key);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'key' => $api_key]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal register: ' . $stmt->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
}
?>
