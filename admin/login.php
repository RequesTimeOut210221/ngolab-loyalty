<?php
session_start();
require_once '../koneksi.php';

// Jika admin sudah login, langsung redirect ke kelola_member
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: kelola_member.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'] ?? '';
    $password = hash('sha256', $_POST['password'] ?? '');

    // Cek di TABEL_USER terlebih dahulu
    $stmt = $conn->prepare("SELECT * FROM TABEL_USER WHERE no_hp = ? OR nim = ? OR username = ? OR email = ?");
    $stmt->bind_param("ssss", $identifier, $identifier, $identifier, $identifier);
    $stmt->execute();
    $resUser = $stmt->get_result();

    if ($resUser->num_rows > 0) {
        $user = $resUser->fetch_assoc();
        if ($user['password'] === $password) {
            // Authentication berhasil untuk Member
            $apiKey = $user['api_key'];
            $username = $user['username'];
            $email = $user['email'];
            $points = $user['saldo_poin'];
            $nim = $user['nim'];
            
            // Menggunakan JS untuk set localStorage dan redirect ke frontend SPA
            echo "<script>
                localStorage.setItem('ngolab_api_key', '$apiKey');
                localStorage.setItem('ngolab_username', '$username');
                localStorage.setItem('ngolab_email', '$email');
                localStorage.setItem('ngolab_points', '$points');
                localStorage.setItem('ngolab_nim', '$nim');
                window.location.href = '../index.html';
            </script>";
            exit;
        }
    }

    // Jika bukan member, cek di TABEL_STAFF
    $stmt2 = $conn->prepare("SELECT * FROM TABEL_STAFF WHERE username = ? OR email = ?");
    $stmt2->bind_param("ss", $identifier, $identifier);
    $stmt2->execute();
    $resStaff = $stmt2->get_result();

    if ($resStaff->num_rows > 0) {
        $staff = $resStaff->fetch_assoc();
        if ($staff['password'] === $password) {
            // Authentication berhasil untuk Admin/Staff
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $staff['username'];
            $_SESSION['admin_role'] = $staff['role'];
            
            header('Location: kelola_member.php');
            exit;
        }
    }

    $error = "Identitas atau Kata Sandi salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unified Login - Ngolab Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl">
    <div class="text-center mb-6">
        <span class="text-4xl">☕</span>
        <h3 class="text-2xl font-bold text-slate-800 mt-2">Masuk ke Ngolab</h3>
        <p class="text-sm text-gray-500 mt-1">Satu pintu untuk Member & Staff</p>
    </div>
    
    <?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
        <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Email / Username / No HP / NIM</label>
            <input type="text" name="identifier" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Kata Sandi</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
        </div>
        <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 rounded-xl hover:bg-slate-900 transition-all">
            Login Sekarang
        </button>
    </form>
    
    <div class="mt-6 text-center text-sm text-gray-500">
        <a href="../index.html" class="hover:underline text-orange-500">← Kembali ke Halaman Utama</a>
    </div>
</div>

</body>
</html>
