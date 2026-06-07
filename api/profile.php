<?php
header('Content-Type: application/json');
require_once '../koneksi.php';

// Auth middleware for consumer API
$headers = getallheaders();
$api_key = $headers['x-api-key'] ?? ($headers['X-Api-Key'] ?? '');

if (empty($api_key)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing x-api-key header']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM TABEL_USER WHERE api_key = ?");
$stmt->bind_param("s", $api_key);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API Key']);
    exit;
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'username' => $user['username'],
        'email' => $user['email'],
        'nim' => $user['nim'],
        'no_hp' => $user['no_hp'],
        'saldo_poin' => (int)$user['saldo_poin'],
        'is_shared_sosmed' => (bool)$user['is_shared_sosmed'],
        'avatar' => $user['avatar']
    ]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    if ($action === 'update') {
        $username = $_POST['username'] ?? '';
        if (empty($username)) {
            // Check for JSON
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? $user['username'];
        }
        
        $avatar_url = $user['avatar'];
        
        // Handle file upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $file_name = 'avatar_' . $user['id_user'] . '_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                // Return path relative to document root
                $avatar_url = 'uploads/profiles/' . $file_name;
            }
        }
        
        $update_stmt = $conn->prepare("UPDATE TABEL_USER SET username = ?, avatar = ? WHERE id_user = ?");
        $update_stmt->bind_param("ssi", $username, $avatar_url, $user['id_user']);
        
        if ($update_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated', 'avatar' => $avatar_url]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
    }
}
?>
