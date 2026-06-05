<?php
include '../koneksi.php';
/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = $_POST['id_feedback'];
    $query = mysqli_query($conn, "DELETE FROM feedback WHERE id_feedback='$id'");
    if (!$query) die("Gagal Hapus Ulasan: " . mysqli_error($conn));
    header("Location: kelola_feedback.php");
    exit;
}

$feedbacks = mysqli_query($conn, "SELECT * FROM feedback ORDER BY id_feedback DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Kelola Feedback - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-gray-100 min-h-screen text-slate-800 flex flex-col md:flex-row">

    <!-- 📂 Admin Sidebar Navigation -->
    <aside class="w-full md:w-64 bg-slate-900 text-gray-300 flex flex-col justify-between shrink-0 border-r border-slate-800">
        <div>
            <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-950">
                <span class="text-xl mr-2 animate-pulse">☕</span>
                <span class="font-extrabold text-sm tracking-wider text-orange-500">NGOLAB POS ADMIN</span>
            </div>
            <nav class="p-4 space-y-1">
                <a href="../showcase_admin.html" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition text-gray-400 hover:bg-slate-800 hover:text-white">
                    <span>📂</span> <span>Dashboard POS</span>
                </a>
                <a href="kelola_menu.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition text-gray-400 hover:bg-slate-800 hover:text-white">
                    <span>🍔</span> <span>Kelola Menu</span>
                </a>
                <a href="kelola_kategori.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition text-gray-400 hover:bg-slate-800 hover:text-white">
                    <span>🏷️</span> <span>Kelola Kategori</span>
                </a>
                <a href="kelola_feedback.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition bg-orange-500 text-white shadow-sm">
                    <span>💬</span> <span>Kelola Feedback</span>
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
            <a href="../index.html" class="text-xs text-red-400 font-bold hover:underline w-full text-center">Keluar ke Aplikasi</a>
        </div>
    </aside>

    <!-- 💻 Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shrink-0 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800">Kelola Feedback</h2>
        </header>

        <main class="flex-grow p-6 overflow-y-auto">
            <div class="container" style="max-width: 100%; margin: 0; padding: 0;">
                <h1>💬 Kelola Ulasan (Feedback)</h1>

                <div class="card">
                    <h2>Daftar Ulasan Masuk dari Pelanggan</h2>

                    <div class="list-container">
                        <?php while ($row = mysqli_fetch_assoc($feedbacks)): ?>
                            <div class="list-item">
                                <div class="list-item-content">
                                    <div class="flex-row">
                                        <strong><?= $row['nama_user'] ?></strong>
                                        <span class="rating"><?= str_repeat('★', $row['rating']) ?></span>
                                    </div>
                                    <p>"<?= $row['ulasan'] ?>"</p>
                                    <p class="text-gray"><small><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></small></p>
                                </div>

                                <!-- Aksi Hapus Ulasan (Moderasi) -->
                                <form method="POST" onsubmit="return confirm('Anda yakin ingin menghapus ulasan ini?');">
                                    <input type="hidden" name="hapus" value="1">
                                    <input type="hidden" name="id_feedback" value="<?= $row['id_feedback'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        <?php endwhile; ?>

                        <?php if (mysqli_num_rows($feedbacks) == 0) echo "<p class='text-center text-gray'>Belum ada ulasan.</p>"; ?>
                    </div>
                </div>
            </div>
</body>

</html>